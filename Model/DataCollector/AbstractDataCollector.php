<?php

namespace ClawRock\Debug\Model\DataCollector;

abstract class AbstractDataCollector implements DataCollectorInterface, \Serializable
{
    const NAME = 'abstract';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    protected $helper;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper
    ) {
        $this->helper = $helper;
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    public function getCollectorName()
    {
        return static::NAME;
    }

    public function getBlockName()
    {
        return sprintf(self::COLLECTOR_PLACEHOLDER, $this->getCollectorName());
    }

    protected function getBacktrace($options = DEBUG_BACKTRACE_PROVIDE_OBJECT)
    {
        if (!function_exists('debug_backtrace')) {
            return false;
        }

        return debug_backtrace($options);
    }
}
