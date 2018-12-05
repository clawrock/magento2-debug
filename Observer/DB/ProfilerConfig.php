<?php

namespace ClawRock\Debug\Observer\DB;

use ClawRock\Debug\Helper\Profiler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;

class ProfilerConfig implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\Manager
     */
    private $messageManager;

    /**
     * @var \ClawRock\Debug\Model\DB\ProfilerConfig
     */
    private $dbProfilerConfig;

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    private $profilerHelper;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \ClawRock\Debug\Model\DB\ProfilerConfig $dbProfilerConfig,
        \ClawRock\Debug\Helper\Profiler $profilerHelper
    ) {
        $this->messageManager = $messageManager;
        $this->dbProfilerConfig = $dbProfilerConfig;
        $this->profilerHelper = $profilerHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!in_array(Profiler::CONFIG_DATA_COLLECTOR_DATABASE, $observer->getChangedPaths())) {
            return;
        }

        try {
            $this->dbProfilerConfig->save($this->profilerHelper->isDatabaseDataCollectorEnabled());
        } catch (FileSystemException $e) {
            $this->messageManager->addExceptionMessage($e);
        }
    }
}
