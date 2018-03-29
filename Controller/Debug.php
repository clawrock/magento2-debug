<?php

namespace ClawRock\Debug\Controller;

use Magento\Framework\App\Action\Action;

abstract class Debug extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \ClawRock\Debug\Model\Profiler
     */
    protected $profiler;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Model\Profiler $profiler
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->profiler = $profiler;
    }
}
