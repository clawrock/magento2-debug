<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;

class Cache extends Collector
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->typeList = $typeList;
    }

    public function getCacheTypes()
    {
        return $this->typeList->getTypes();
    }

    public function isInvalidated($type)
    {
        return in_array($type, array_keys($this->typeList->getInvalidated()));
    }
}
