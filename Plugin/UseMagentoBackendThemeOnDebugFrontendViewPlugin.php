<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin;

use Magento\Framework\App\Area;

class UseMagentoBackendThemeOnDebugFrontendViewPlugin
{
    public function __construct(
        private \Magento\Framework\View\DesignInterface $design
    ) {
    }

    public function beforeExecute(): void
    {
        $this->design->setArea(Area::AREA_ADMINHTML);
        $this->design->setDesignTheme('Magento/backend');
    }
}
