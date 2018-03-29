<?php

namespace ClawRock\Debug\Controller;

use Magento\Framework\App\Action\Action;

abstract class Cache extends Action
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $cacheState;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState
    ) {
        parent::__construct($context);

        $this->cacheTypeList = $cacheTypeList;
        $this->cacheState = $cacheState;
    }

    protected function isValidCacheType($type)
    {
        return in_array($type, array_keys($this->cacheTypeList->getTypes()));
    }
}
