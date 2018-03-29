<?php

namespace ClawRock\Debug\Model\DataCollector;

class TimeDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{
    const NAME = 'time';

    const EVENTS     = 'events';
    const DURATION   = 'duration';
    const START_TIME = 'start_time';

    /**
     * @var \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver
     */
    private $stopwatchDriver;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver
    ) {
        parent::__construct($helper);
        $this->stopwatchDriver = $stopwatchDriver;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        $this->data = [
            self::START_TIME => $_SERVER['REQUEST_TIME_FLOAT'],
            self::EVENTS     => [],
        ];

        return $this;
    }


    public function lateCollect()
    {
        $this->setEvents($this->stopwatchDriver->getEvents());
        $this->data[self::DURATION] = (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000;

        return $this;
    }

    public function setEvents(array $events)
    {
        /** @var \Symfony\Component\Stopwatch\StopwatchEvent $event */
        foreach ($events as $event) {
            $event->ensureStopped();
        }

        $this->data[self::EVENTS] = $events;
    }

    public function getDuration()
    {
        return $this->data[self::DURATION] ?? 0;
    }

    public function getStartTime()
    {
        return $this->data[self::START_TIME] ?? 0;
    }

    public function getEvents()
    {
        return $this->data[self::EVENTS] ?? [];
    }

    public function getEvent($event)
    {
        return $this->data[self::EVENTS][$event] ?? null;
    }

    public function isEnabled()
    {
        return $this->helper->isTimeDataCollectorEnabled();
    }
}
