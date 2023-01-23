<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver;

class TimeCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'time';
    public const EVENTS = 'events';
    public const DURATION = 'duration';
    public const START_TIME = 'start_time';

    public const EVENT_TYPES = [
        StopwatchDriver::CATEGORY_CORE,
        StopwatchDriver::CATEGORY_ROUTING,
        StopwatchDriver::CATEGORY_CONFIG,
        StopwatchDriver::CATEGORY_EVENT,
        StopwatchDriver::CATEGORY_LAYOUT,
        StopwatchDriver::CATEGORY_EAV,
        StopwatchDriver::CATEGORY_CONTROLLER,
        StopwatchDriver::CATEGORY_TEMPLATE,
        StopwatchDriver::CATEGORY_DEBUG,
        StopwatchDriver::CATEGORY_UNKNOWN,
    ];

    public const ERROR_THRESHOLD = 2000;
    public const WARNING_THRESHOLD = 1000;

    private \Magento\Framework\Serialize\SerializerInterface $serializer;
    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver;
    private \ClawRock\Debug\Helper\Formatter $formatter;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;

    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver,
        \ClawRock\Debug\Helper\Formatter $formatter,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage
    ) {
        $this->serializer = $serializer;
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->stopwatchDriver = $stopwatchDriver;
        $this->formatter = $formatter;
        $this->profileMemoryStorage = $profileMemoryStorage;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return \ClawRock\Debug\Model\Collector\CollectorInterface
     */
    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageWarning
            self::START_TIME => $_SERVER['REQUEST_TIME_FLOAT'],
            self::DURATION   => 0,
            self::EVENTS     => [],
        ]);

        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $events = $this->stopwatchDriver->getEvents();
        /** @var \Symfony\Component\Stopwatch\StopwatchEvent $event */
        foreach ($events as $event) {
            $event->ensureStopped();
        }

        $this->dataCollector->addData(self::EVENTS, $events);

        $this->dataCollector->addData(
            self::DURATION,
            microtime(true) - $this->dataCollector->getData(self::START_TIME)
        );

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->config->isTimeCollectorEnabled();
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

    public function getDuration(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::DURATION) ?? 0, 0);
    }

    public function getStartTime(): int
    {
        return $this->dataCollector->getData(self::START_TIME) ?? 0;
    }

    public function getEvents(): array
    {
        return $this->dataCollector->getData(self::EVENTS) ?? [];
    }

    public function getEvent(string $event): ?\Symfony\Component\Stopwatch\StopwatchEvent
    {
        return $this->dataCollector->getData(self::EVENTS)[$event] ?? null;
    }

    public function getColors(): array
    {
        $colors = [];
        foreach (self::EVENT_TYPES as $eventType) {
            $color = $this->config->getPerformanceColor($eventType);
            $colors[$eventType] = $color ?: StopwatchDriver::CATEGORY_UNKNOWN;
        }

        return $colors;
    }

    public function getJsColors(): string
    {
        return (string) $this->serializer->serialize($this->getColors());
    }

    public function getJsTimeline(): string
    {
        $mainEvent = $this->getEvent('__section__');
        if (!$mainEvent) {
            return (string) $this->serializer->serialize([]);
        }

        return (string) $this->serializer->serialize([
            'max' => sprintf('%f', $mainEvent->getEndTime()),
            'data' => [$this->getTimelineData()],
        ]);
    }

    private function getTimelineData(): array
    {
        $data = [];

        $events = $this->getEvents();

        foreach ($events as $name => $event) {
            /** @var \Symfony\Component\Stopwatch\StopwatchEvent $event */
            if ($name === '__section__') {
                continue;
            }
            $periods = [];
            foreach ($event->getPeriods() as $period) {
                $periods[] = [
                    'start' => sprintf('%f', $period->getStartTime()),
                    'end'   => sprintf('%f', $period->getEndTime()),
                ];
            }

            $data[] = [
                'name'      => $name,
                'category'  => $event->getCategory(),
                'origin'    => sprintf('%f', $event->getOrigin()),
                'starttime' => sprintf('%f', $event->getStartTime()),
                'endtime'   => sprintf('%f', $event->getEndTime()),
                'duration'  => sprintf('%f', $event->getDuration()),
                'memory'    => sprintf('%.1f', $event->getMemory() / 1024 / 1024),
                'periods'   => $periods,
            ];
        }

        /** @var \Symfony\Component\Stopwatch\StopwatchEvent $mainEvent */
        $mainEvent = $events['__section__'];

        return [
            'id'     => $this->profileMemoryStorage->read()->getToken(),
            'left'   => $mainEvent->getStartTime(),
            'events' => $data,
        ];
    }

    public function getStatus(): string
    {
        if ($this->getDuration() > self::ERROR_THRESHOLD) {
            return self::STATUS_ERROR;
        }

        if ($this->getDuration() > self::WARNING_THRESHOLD) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }
}
