<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Profiler\Driver;

use Magento\Framework\Profiler;
use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchDriver implements \Magento\Framework\Profiler\DriverInterface
{
    public const ROOT_EVENT = 'magento';
    public const CATEGORY_CORE = 'core';
    public const CATEGORY_CONFIG = 'config';
    public const CATEGORY_LAYOUT = 'layout';
    public const CATEGORY_EVENT = 'event';
    public const CATEGORY_EAV = 'eav';
    public const CATEGORY_CONTROLLER = 'controller';
    public const CATEGORY_TEMPLATE = 'template';
    public const CATEGORY_ROUTING = 'routing';
    public const CATEGORY_DEBUG = 'debug';
    public const CATEGORY_UNKNOWN = 'unknown';

    private \Symfony\Component\Stopwatch\Stopwatch $stopwatch;

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
    public function start($timerId, ?array $tags = null)
    {
        $timerId = $this->stripNesting($timerId);
        $category = $this->getCategory((string) $timerId);
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
    public function clear($timerId = null) // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    {
    }

    public function getEvents(): array
    {
        $this->stopwatch->stopSection('magento');

        return $this->stopwatch->getSectionEvents('magento');
    }

    /**
     * @param string $timerId
     * @return string
     */
    private function stripNesting($timerId)
    {
        $timerName = strrchr($timerId, Profiler::NESTING_SEPARATOR);

        return $timerName ? substr($timerName, strlen(Profiler::NESTING_SEPARATOR)) : $timerId;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param string $timerId
     * @return string
     */
    private function getCategory(string $timerId): string
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

    private function isCoreTimer(string $timerId): bool
    {
        [$namespace] = explode(':', $timerId);
        return strtolower($namespace) === 'magento' || strtolower($namespace) === 'core';
    }

    private function isEventTimer(string $timerId): bool
    {
        [$namespace] = explode(':', $timerId);
        return strtolower($namespace) === 'event' || strtolower($namespace) === 'observer';
    }

    private function isConfigTimer(string $timerId): bool
    {
        [$namespace] = explode(':', $timerId);
        return strtolower($namespace) === 'load_area';
    }

    private function isControllerTimer(string $timerId): bool
    {
        [$namespace] = explode(':', $timerId);
        return strtolower($namespace) === 'controller_action';
    }

    private function isEavTimer(string $timerId): bool
    {
        [$namespace] = explode(':', $timerId);
        return strtolower($namespace) === 'eav';
    }

    private function isTemplateTimer(string $timerId): bool
    {
        return substr($timerId, -6) === '.phtml' || strpos(strtolower($timerId), 'template') === 0;
    }

    private function isRoutingTimer(string $timerId): bool
    {
        return $timerId === 'routers_match' || $timerId === 'store.resolve';
    }

    private function isLayoutTimer(string $timerId): bool
    {
        return strpos(strtolower($timerId), 'layout') === 0;
    }

    private function isDebugTimer(string $timerId): bool
    {
        return strpos(strtolower($timerId), 'debug::profiler') === 0;
    }
}
