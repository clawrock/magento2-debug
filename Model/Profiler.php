<?php

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use ClawRock\Debug\Model\DataCollector\LateDataCollectorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Profiler as MagentoProfiler;

class Profiler
{
    const URL_TOKEN_PARAMETER      = 'token';
    const TOOLBAR_FULL_ACTION_NAME = 'debug_profiler_toolbar';

    /**
     * @var null|DataCollectorInterface[]
     */
    private $dataCollectors = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    private $helper;

    /**
     * @var \ClawRock\Debug\Model\Profiler\StorageInterface
     */
    private $storage;

    /**
     * @var \ClawRock\Debug\Model\Profile\Storage
     */
    private $profileStorage;

    /**
     * @var \ClawRock\Debug\Helper\Toolbar
     */
    private $toolbar;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Model\Profiler\StorageInterface $storage,
        \ClawRock\Debug\Model\Profile\Storage $profileStorage,
        \ClawRock\Debug\Helper\Toolbar $toolbar,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->objectManager = $objectManager;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->storage = $storage;
        $this->profileStorage = $profileStorage;
        $this->toolbar = $toolbar;
        $this->logger = $logger;
    }

    public function run(HttpRequest $request, HttpResponse $response)
    {
        if (!$this->isAvailable() || !$this->helper->isAllowedIP()) {
            return;
        }

        try {
            $profile  = $this->collect($request, $response);
            if ($profile) {
                $this->profileStorage->addItem($profile);
            }
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
            $url = $this->helper->getUrl($token);
            $response->setHeader('X-Debug-Token-Link', $url);
        }

        $this->toolbar->inject($request, $response, $token);

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

            $collectors = $this->helper->getDataCollectors();
            foreach ($collectors as $class) {
                $collector = $this->objectManager->get($class);
                if (!$collector instanceof DataCollectorInterface) {
                    throw new \InvalidArgumentException('Collector must implement "DataCollectorInterface"');
                }

                if ($collector->isEnabled()) {
                    $this->dataCollectors[$collector->getCollectorName()] = $collector;
                }
            }
        }

        return $this->dataCollectors;
    }

    public function collect(HttpRequest $request, HttpResponse $response)
    {
        $start = microtime(true);
        $profile = new Profile(substr(hash('sha256', uniqid(mt_rand(), true)), 0, 6));
        $profile->setTime(time());
        $profile->setUrl($request->getRequestString() ? $request->getRequestString() : '/');
        $profile->setMethod($request->getMethod());
        $profile->setStatusCode($response->getHttpResponseCode());
        $profile->setIp($request->getClientIp());

        $response->setHeader('X-Debug-Token', $profile->getToken());

        $profileKey = 'DEBUG::profiler::collect';
        MagentoProfiler::start($profileKey);
        foreach ($this->getDataCollectors() as $collector) {
            $profileCollectorKey = $profileKey . '::' . $collector->getCollectorName();
            /** @var DataCollectorInterface $collector */
            MagentoProfiler::start($profileCollectorKey);
            $collector->collect($request, $response);
            MagentoProfiler::stop($profileCollectorKey);
            $profile->addCollector($collector);
        }
        MagentoProfiler::stop($profileKey);
        $collectTime = microtime(true) - $start;
        $profile->setCollectTime($collectTime);

        return $profile;
    }

    public function loadProfile($token)
    {
        return $this->storage->read($token);
    }

    public function find($ip, $url, $limit, $method, $start, $end)
    {
        return $this->storage->find($ip, $url, $limit, $method, $this->getTimestamp($start), $this->getTimestamp($end));
    }

    private function getTimestamp($value)
    {
        if (null === $value || '' == $value) {
            return null;
        }

        try {
            $value = new \DateTime(is_numeric($value) ? '@' . $value : $value);
        } catch (\Exception $e) {
            return null;
        }

        return $value->getTimestamp();
    }

    /**
     * @param Profile $profile
     * @throws \Exception
     */
    public function saveProfile(Profile $profile)
    {
        foreach ($profile->getCollectors() as $collector) {
            if ($collector instanceof LateDataCollectorInterface) {
                $collector->lateCollect();
            }
        }

        if (!$this->storage->write($profile)) {
            throw new \Exception('Unable to store the profiler information.', [
                'configured_storage' => get_class($this->storage),
            ]);
        }
    }

    public function isAvailable(): bool
    {
        return $this->registry->registry('current_profile') instanceof \ClawRock\Debug\Model\Profile;
    }

    public function flush()
    {
        return $this->storage->purge();
    }

    public function onTerminate()
    {
        try {
            /** @var \ClawRock\Debug\Model\Profile $profile */
            foreach ($this->profileStorage as $profile) {
                $this->saveProfile($profile);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
