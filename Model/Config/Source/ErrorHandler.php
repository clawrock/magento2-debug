<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ErrorHandler implements OptionSourceInterface
{
    public const MAGENTO = '0';
    public const WHOOPS  = 'whoops';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::MAGENTO, 'label' => __('Default')],
            ['value' => self::WHOOPS, 'label' => __('Whoops')],
        ];
    }

    public function toArray(): array
    {
        return [
            self::MAGENTO => __('Default'),
            self::WHOOPS  => __('Whoops'),
        ];
    }
}
