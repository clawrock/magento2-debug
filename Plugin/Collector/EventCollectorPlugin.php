<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\EventObserver;
use Magento\Framework\Event\Invoker\InvokerDefault;
use Magento\Framework\Event\Observer;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventCollectorPlugin
{
    public function __construct(
        private \ClawRock\Debug\Helper\Config $config,
        private \ClawRock\Debug\Model\Collector\EventCollector $eventCollector
    ) {
    }

    public function aroundDispatch(
        InvokerDefault $subject,
        callable $proceed,
        array $configuration,
        Observer $observer
    ): void {
        if (!$this->config->isEnabled()) {
            $proceed($configuration, $observer);

            return;
        }

        $start = microtime(true);
        $proceed($configuration, $observer);
        $end = microtime(true);

        if (array_key_exists('name', $configuration) && array_key_exists('instance', $configuration)) {
            $this->eventCollector->log(new EventObserver(
                $configuration['name'],
                $configuration['instance'],
                $observer->getEvent()->getName(),
                $end - $start
            ));
        }
    }
}
