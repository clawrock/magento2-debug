<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;
use ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver;

class Time extends Collector
{
    public function getJsTimeline()
    {
        /** @var \ClawRock\Debug\Model\DataCollector\TimeDataCollector $collector */
        $collector = $this->getCollector();
        /** @var \Symfony\Component\Stopwatch\StopwatchEvent $mainEvent */
        $mainEvent = $collector->getEvent('__section__');
        if (!$mainEvent) {
            return json_encode([]);
        }

        return json_encode([
            'max' => sprintf('%f', $mainEvent->getEndTime()),
            'data' => [$this->getTimelineData()],
        ]);
    }

    public function getColors()
    {
        return [
            StopwatchDriver::CATEGORY_CORE => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_CORE),
            StopwatchDriver::CATEGORY_ROUTING => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_ROUTING),
            StopwatchDriver::CATEGORY_CONFIG => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_CONFIG),
            StopwatchDriver::CATEGORY_EVENT => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_EVENT),
            StopwatchDriver::CATEGORY_LAYOUT => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_LAYOUT),
            StopwatchDriver::CATEGORY_EAV => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_EAV),
            StopwatchDriver::CATEGORY_CONTROLLER => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_CONTROLLER),
            StopwatchDriver::CATEGORY_TEMPLATE => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_TEMPLATE),
            StopwatchDriver::CATEGORY_DEBUG => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_DEBUG),
            StopwatchDriver::CATEGORY_UNKNOWN => $this->helper->getPerformanceColor(StopwatchDriver::CATEGORY_UNKNOWN),
        ];
    }

    public function getJsColors()
    {
        return json_encode($this->getColors());
    }

    protected function getTimelineData()
    {
        $data = [];

        /** @var \ClawRock\Debug\Model\DataCollector\TimeDataCollector $collector */
        $collector = $this->getCollector();
        $events = $collector->getEvents();

        foreach ($events as $name => $event) {
            /** @var \Symfony\Component\Stopwatch\StopwatchEvent $event */
            if ($name === '__section__') {
                continue;
            }
            $periods = [];
            foreach ($event->getPeriods() as $period) {
                $periods[] = [
                    'start' => sprintf('%f', $period->getStartTime()),
                    'end'   => sprintf('%f', $period->getEndTime())
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
                'periods'   => $periods
            ];
        }

        /** @var \Symfony\Component\Stopwatch\StopwatchEvent $mainEvent */
        $mainEvent = $events['__section__'];

        return [
            'id'     => $this->getToken(),
            'left'   => $mainEvent->getStartTime(),
            'events' => $data
        ];
    }
}
