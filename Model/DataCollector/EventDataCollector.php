<?php

namespace ClawRock\Debug\Model\DataCollector;

class EventDataCollector extends AbstractDataCollector
{
    const NAME = 'event';

    const TIME      = 'time';
    const EVENTS    = 'events';
    const OBSERVERS = 'observers';

    protected $data = [
        self::TIME      => 0,
        self::EVENTS    => [],
        self::OBSERVERS => [],
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this;
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        // Data is collected by \ClawRock\Debug\Plugin\Collector\EventDataCollectorPlugin
        return $this;
    }

    public function addObserver(array $observer)
    {
        if ($this->isDebugObserver($observer['class'])) {
            return;
        }
        $this->addEvent($observer['event'], $observer['class'], $observer['time']);
        $this->addTime($observer['time']);
        $this->data[self::OBSERVERS][] = $observer;
    }

    public function isEnabled()
    {
        return $this->helper->isEventDataCollectorEnabled();
    }

    public function getTime()
    {
        return sprintf('%0.2f', ($this->data[self::TIME] ?? 0));
    }

    public function getObserversCount()
    {
        return count($this->data[self::OBSERVERS] ?? []);
    }

    public function getEventsCount()
    {
        return count($this->data[self::EVENTS] ?? []);
    }

    public function getEvents()
    {
        return $this->data[self::EVENTS] ?? [];
    }

    public function getObservers()
    {
        return $this->data[self::OBSERVERS] ?? [];
    }

    protected function addTime($time)
    {
        $this->data[self::TIME] += $time;
    }

    protected function addEvent($event, $observer, $time)
    {
        if (!isset($this->data[self::EVENTS][$event])) {
            $this->data[self::EVENTS][$event] = [
                'observers' => [],
                'count'     => 0,
                'time'      => 0,
            ];
        }
        $this->data[self::EVENTS][$event]['observers'][] = $observer;
        $this->data[self::EVENTS][$event]['count']++;
        $this->data[self::EVENTS][$event]['time'] += $time;
    }

    protected function isDebugObserver($class)
    {
        return strpos($class, 'ClawRock\Debug') === 0;
    }
}
