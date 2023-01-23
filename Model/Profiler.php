<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Collector\LateCollectorInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\Profiler as MagentoProfiler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Profiler
{
    public const URL_TOKEN_PARAMETER = 'token';
    public const URL_PANEL_PARAMETER = 'panel';
    public const TOOLBAR_FULL_ACTION_NAME = 'debug_profiler_toolbar';

    /** @var null|\ClawRock\Debug\Model\Collector\CollectorInterface[] */
    private ?array $dataCollectors = null;
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\ProfileFactory $profileFactory;
    private \ClawRock\Debug\Helper\Url $urlHelper;
    private \ClawRock\Debug\Helper\Injector $injector;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository;
    private \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage;
    private \ClawRock\Debug\Logger\Logger $logger;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\ProfileFactory $profileFactory,
        \ClawRock\Debug\Helper\Url $urlHelper,
        \ClawRock\Debug\Helper\Injector $injector,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository,
        \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage,
        \ClawRock\Debug\Logger\Logger $logger
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->profileFactory = $profileFactory;
        $this->urlHelper = $urlHelper;
        $this->injector = $injector;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->profileRepository = $profileRepository;
        $this->httpStorage = $httpStorage;
        $this->logger = $logger;
    }

    public function run(Request $request, Response $response): void
    {
        if (!$this->config->isAllowedIP()) {
            return;
        }

        try {
            $profile  = $this->collect($request, $response);
            $this->profileMemoryStorage->write($profile);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return;
        }

        $token = null;
        /** @var \Laminas\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header->getFieldName() === 'X-Debug-Token') {
                $token = $header->getFieldValue();
                break;
            }
        }

        if ($token) {
            $response->setHeader('X-Debug-Token-Link', $this->urlHelper->getProfilerUrl($token));
        }

        $this->injector->inject($request, $response, $token);

        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        register_shutdown_function([$this, 'onTerminate']);
    }

    public function getDataCollector(string $name): ?CollectorInterface
    {
        $collectors = $this->getDataCollectors();

        return $collectors[$name] ?? null;
    }

    public function getDataCollectors(): array
    {
        if ($this->dataCollectors === null) {
            $this->dataCollectors = [];

            $collectors = $this->config->getCollectors();
            foreach ($collectors as $class) {
                $collector = $this->objectManager->get($class);
                if (!$collector instanceof CollectorInterface) {
                    throw new \InvalidArgumentException('Collector must implement "CollectorInterface"');
                }

                if ($collector->isEnabled()) {
                    $this->dataCollectors[$collector->getName()] = $collector;
                }
            }
        }

        return $this->dataCollectors;
    }

    public function collect(Request $request, Response $response): Profile
    {
        $start = microtime(true);
        /** @var \ClawRock\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create([
            // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
            'token' => substr(hash('sha256', uniqid((string) mt_rand(), true)), 0, 6),
        ]);
        $profile->setUrl($request->getRequestString() ? $request->getRequestString() : '/');
        $profile->setMethod($request->getMethod());
        $profile->setRoute($this->urlHelper->getRequestFullActionName($request));
        $profile->setStatusCode($response->getHttpResponseCode());
        $profile->setIp($request->getClientIp());

        $response->setHeader('X-Debug-Token', $profile->getToken());

        $this->httpStorage->setRequest($request);
        $this->httpStorage->setResponse($response);

        $profileKey = 'DEBUG::profiler::collect';
        MagentoProfiler::start($profileKey);
        foreach ($this->getDataCollectors() as $collector) {
            $profileCollectorKey = $profileKey . '::' . $collector->getName();
            /** @var \ClawRock\Debug\Model\Collector\CollectorInterface $collector */
            MagentoProfiler::start($profileCollectorKey);
            $collector->collect();
            MagentoProfiler::stop($profileCollectorKey);
            $profile->addCollector($collector);
        }
        MagentoProfiler::stop($profileKey);
        $profile->setTime(time());
        $collectTime = microtime(true) - $start;
        $profile->setCollectTime($collectTime);

        return $profile;
    }

    public function onTerminate(): void
    {
        try {
            $profile = $this->profileMemoryStorage->read();
            foreach ($profile->getCollectors() as $collector) {
                if ($collector instanceof LateCollectorInterface) {
                    $collector->lateCollect();
                }
            }

            $this->profileRepository->save($profile);
        } catch (\Exception $e) {
            $this->logger->error('ClawRock_Debug: onTerminate error', ['exception' => $e]);
        }
    }
}
