<?php

namespace ClawRock\Debug\Model\Profiler\Driver;

use Magento\Framework\Profiler;
use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchDriver implements \Magento\Framework\Profiler\DriverInterface
{
    const ROOT_EVENT = 'magento';

    const CATEGORY_CORE = 'core';
    const CATEGORY_CONFIG = 'config';
    const CATEGORY_LAYOUT = 'layout';
    const CATEGORY_EVENT = 'event';
    const CATEGORY_EAV = 'eav';
    const CATEGORY_CONTROLLER = 'controller';
    const CATEGORY_TEMPLATE = 'template';
    const CATEGORY_ROUTING = 'routing';
    const CATEGORY_DEBUG = 'debug';
    const CATEGORY_UNKNOWN = 'unknown';

    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    private $stopwatch;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch(true);
        $this->stopwatch->openSection();
    }

    /**
     * Start timer
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $timerId
     * @param array|null $tags
     * @return void
     */
    public function start($timerId, array $tags = null)
    {
        $timerId = $this->stripNesting($timerId);
        $category = $this->getCategory($timerId);
        $this->stopwatch->start($timerId, $category);
    }

    /**
     * Stop timer
     *
     * @param string $timerId
     * @return void
     */
    public function stop($timerId)
    {
        $timerId = $this->stripNesting($timerId);
        if ($this->stopwatch->isStarted($timerId)) {
            $this->stopwatch->stop($timerId);
        }
    }

    /**
     * Clear collected statistics for specified timer or for whole profiler if timer name is omitted.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string|null $timerId
     * @return void
     */
    public function clear($timerId = null)
    {
    }

    public function getEvents()
    {
        $this->stopwatch->stopSection('magento');

        return $this->stopwatch->getSectionEvents('magento');
    }

    private function stripNesting($timerId)
    {
        $timerName = strrchr($timerId, Profiler::NESTING_SEPARATOR);

        return $timerName ? substr($timerName, strlen(Profiler::NESTING_SEPARATOR)) : $timerId;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param $timerId
     * @return string
     */
    private function getCategory($timerId)
    {
        if ($this->isCoreTimer($timerId)) {
            return self::CATEGORY_CORE;
        }

        if ($this->isEventTimer($timerId)) {
            return self::CATEGORY_EVENT;
        }

        if ($this->isConfigTimer($timerId)) {
            return self::CATEGORY_CONFIG;
        }

        if ($this->isControllerTimer($timerId)) {
            return self::CATEGORY_CONTROLLER;
        }

        if ($this->isEavTimer($timerId)) {
            return self::CATEGORY_EAV;
        }

        if ($this->isTemplateTimer($timerId)) {
            return self::CATEGORY_TEMPLATE;
        }

        if ($this->isRoutingTimer($timerId)) {
            return self::CATEGORY_ROUTING;
        }

        if ($this->isLayoutTimer($timerId)) {
            return self::CATEGORY_LAYOUT;
        }

        if ($this->isDebugTimer($timerId)) {
            return self::CATEGORY_DEBUG;
        }

        return self::CATEGORY_UNKNOWN;
    }

    private function isCoreTimer($timerId)
    {
        list($namespace) = explode(':', $timerId);
        return strtolower($namespace) === 'magento' || strtolower($namespace) === 'core';
    }

    private function isEventTimer($timerId)
    {
        list($namespace) = explode(':', $timerId);
        return strtolower($namespace) === 'event' || strtolower($namespace) === 'observer';
    }

    private function isConfigTimer($timerId)
    {
        list($namespace) = explode(':', $timerId);
        return strtolower($namespace) === 'load_area';
    }

    private function isControllerTimer($timerId)
    {
        list($namespace) = explode(':', $timerId);
        return strtolower($namespace) === 'controller_action';
    }

    private function isEavTimer($timerId)
    {
        list($namespace) = explode(':', $timerId);
        return strtolower($namespace) === 'eav';
    }

    private function isTemplateTimer($timerId)
    {
        return substr($timerId, -6) === '.phtml' || strpos(strtolower($timerId), 'template') === 0;
    }

    private function isRoutingTimer($timerId)
    {
        return $timerId === 'routers_match' || $timerId === 'store.resolve';
    }

    private function isLayoutTimer($timerId)
    {
        return strpos(strtolower($timerId), 'layout') === 0;
    }

    private function isDebugTimer($timerId)
    {
        return strpos(strtolower($timerId), 'debug::profiler') === 0;
    }
}
