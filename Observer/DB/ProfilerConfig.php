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
        if (!$this->isDBProfilerDependentConfigChanged($observer->getChangedPaths())) {
            return;
        }

        $flag = $this->profilerHelper->isDatabaseDataCollectorEnabled() && $this->profilerHelper->isEnabled();

        try {
            $this->dbProfilerConfig->save($flag);
        } catch (FileSystemException $e) {
            $this->messageManager->addExceptionMessage($e);
        }
    }

    private function isDBProfilerDependentConfigChanged(array $paths): bool
    {
        return in_array(Profiler::CONFIG_DATA_COLLECTOR_DATABASE, $paths) || in_array(Profiler::CONFIG_ENABLED, $paths);
    }
}
