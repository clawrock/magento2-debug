<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;

class EventCollector implements CollectorInterface, LateCollectorInterface, LoggerCollectorInterface
{
    public const NAME = 'event';
    public const TIME = 'time';
    public const EVENTS = 'events';
    public const OBSERVERS = 'observers';
    public const OBSERVERS_COUNT = 'observers_count';
    public const DISPATCH_COUNT = 'events_count';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Logger\DataLogger $dataLogger;
    private \ClawRock\Debug\Helper\Formatter $formatter;
    private \ClawRock\Debug\Helper\Debug $debug;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Logger\DataLoggerFactory $dataLoggerFactory,
        \ClawRock\Debug\Helper\Formatter $formatter,
        \ClawRock\Debug\Helper\Debug $debug
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLoggerFactory->create();
        $this->formatter = $formatter;
        $this->debug = $debug;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::TIME      => 0,
            self::EVENTS    => [],
            self::OBSERVERS => [],
        ]);

        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $time = $this->dataCollector->getData(self::TIME);
        $observers = $this->dataCollector->getData(self::OBSERVERS);
        $events = $this->dataCollector->getData(self::EVENTS);

        /** @var \ClawRock\Debug\Model\ValueObject\EventObserver $observer */
        foreach ($this->dataLogger->getLogs() as $observer) {
            $time += $observer->getTime();
            if (!isset($events[$observer->getEvent()])) {
                $events[$observer->getEvent()] = [];
            }
            $events[$observer->getEvent()][] = $observer;
            $observers[] = $observer;
        }

        $this->dataCollector->setData([
            self::TIME      => $time,
            self::EVENTS    => $events,
            self::OBSERVERS => $observers,
        ]);

        return $this;
    }

    public function getTime(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::TIME) ?? 0);
    }

    public function getEvents(): array
    {
        return $this->dataCollector->getData(self::EVENTS) ?? [];
    }

    public function getObserversCount(): int
    {
        return array_sum(array_map('count', $this->getEvents()));
    }

    /**
     * @param \ClawRock\Debug\Model\ValueObject\EventObserver[] $observers
     * @return \ClawRock\Debug\Model\ValueObject\EventObserver[]
     */
    public function filterObservers(array $observers): array
    {
        $filtered = [];
        foreach ($observers as $observer) {
            $filtered[$observer->getClass()] = $observer;
        }

        return $filtered;
    }

    public function getEventTime(array $observers): string
    {
        $time = 0;
        /** @var \ClawRock\Debug\Model\ValueObject\EventObserver $observer */
        foreach ($observers as $observer) {
            $time += $observer->getTime();
        }

        return $this->formatter->microtime($time);
    }

    public function isEnabled(): bool
    {
        return $this->config->isEventCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        /** @var \ClawRock\Debug\Model\ValueObject\EventObserver $value */
        if ($this->debug->isDebugClass($value->getClass())) {
            return $this;
        }
        $this->dataLogger->log($value);

        return $this;
    }
}
