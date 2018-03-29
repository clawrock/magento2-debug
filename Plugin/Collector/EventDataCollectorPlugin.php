<?php

namespace ClawRock\Debug\Plugin\Collector;

use Magento\Framework\Event\Invoker\InvokerDefault;
use Magento\Framework\Event\Observer;

class EventDataCollectorPlugin
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\EventDataCollector
     */
    protected $dataCollector;

    public function __construct(
        \ClawRock\Debug\Model\DataCollector\EventDataCollector $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    public function aroundDispatch(InvokerDefault $subject, callable $proceed, array $configuration, Observer $observer)
    {
        $event = $observer->getEvent()->getName();
        $start = microtime(true);
        $proceed($configuration, $observer);
        $end = microtime(true);

        $this->dataCollector->addObserver([
            'event' => $event,
            'class' => $configuration['instance'] ?? 'n/a',
            'time' => sprintf('%0.2f', ($end - $start) * 1000),
        ]);
    }
}
