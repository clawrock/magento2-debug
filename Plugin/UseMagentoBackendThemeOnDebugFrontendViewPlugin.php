<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin;

use Magento\Framework\App\Area;

class UseMagentoBackendThemeOnDebugFrontendViewPlugin
{
    public function __construct(
        private \ClawRock\Debug\Helper\Config $config,
        private \Magento\Framework\View\DesignInterface $design
    ) {
    }

    public function beforeExecute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->design->setArea(Area::AREA_ADMINHTML);
        $this->design->setDesignTheme('Magento/backend');
    }
}
