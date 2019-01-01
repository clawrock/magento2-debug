<?php

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Collector\LateCollectorInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\Profiler as MagentoProfiler;

class Profiler
{
    const URL_TOKEN_PARAMETER      = 'token';
    const URL_PANEL_PARAMETER      = 'panel';
    const TOOLBAR_FULL_ACTION_NAME = 'debug_profiler_toolbar';

    /**
     * @var null|CollectorInterface[]
     */
    private $dataCollectors = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \ClawRock\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * @var \ClawRock\Debug\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \ClawRock\Debug\Helper\Injector
     */
    private $injector;

    /**
     * @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \ClawRock\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var \ClawRock\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    /**
     * @var \ClawRock\Debug\Logger\Logger
     */
    private $logger;

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

    public function run(Request $request, Response $response)
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
        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header->getFieldName() === 'X-Debug-Token') {
                $token = $header->getFieldValue();
                break;
            }
        }

        if ($token) {
            $response->setHeader('X-Debug-Token-Link', $profile->getDebugUrl($token));
        }

        $this->injector->inject($request, $response, $token);

        register_shutdown_function([$this, 'onTerminate']);
    }

    public function getDataCollector($name)
    {
        $collectors = $this->getDataCollectors();

        return isset($collectors[$name]) ? $collectors[$name] : false;
    }

    public function getDataCollectors()
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

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return \ClawRock\Debug\Model\Profile
     */
    public function collect(Request $request, Response $response)
    {
        $start = microtime(true);
        /** @var \ClawRock\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create(['token' => substr(hash('sha256', uniqid(mt_rand(), true)), 0, 6)]);
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
            /** @var CollectorInterface $collector */
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

    public function onTerminate()
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
            $this->logger->critical($e);
        }
    }
}
