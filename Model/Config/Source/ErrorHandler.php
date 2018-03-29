<?php

namespace ClawRock\Debug\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ErrorHandler implements ArrayInterface
{
    const MAGENTO = '0';
    const WHOOPS  = 'whoops';

    public function toOptionArray()
    {
        return [
            ['value' => self::MAGENTO, 'label' => __('Default')],
            ['value' => self::WHOOPS, 'label' => __('Whoops')],
        ];
    }

    public function toArray()
    {
        return [
            self::MAGENTO => __('Default'),
            self::WHOOPS  => __('Whoops'),
        ];
    }
}
