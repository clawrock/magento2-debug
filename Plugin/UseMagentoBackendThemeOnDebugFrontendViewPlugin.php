<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin;

use Magento\Framework\App\Area;

class UseMagentoBackendThemeOnDebugFrontendViewPlugin
{
    private \Magento\Framework\View\DesignInterface $design;

    public function __construct(
        \Magento\Framework\View\DesignInterface $design
    ) {
        $this->design = $design;
    }

    public function beforeExecute(): void
    {
        $this->design->setArea(Area::AREA_ADMINHTML);
        $this->design->setDesignTheme('Magento/backend');
    }
}
