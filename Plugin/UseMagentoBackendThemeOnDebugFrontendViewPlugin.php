<?php

declare(strict_types=1);

namespace ClawRock\Debug\Plugin;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\ResultInterface;
class UseMagentoBackendThemeOnDebugFrontendViewPlugin
{
    private string $currentTheme = '';
    public function __construct(
        private \Magento\Framework\View\DesignInterface $design
    ) {
    }

    public function beforeExecute(): void
    {
        $this->currentTheme = $this->design->getDesignTheme()->getThemePath();
        $this->design->setArea(Area::AREA_ADMINHTML);
        $this->design->setDesignTheme('Magento/backend');
    }

    public function afterExecute(HttpGetActionInterface $subject, ?ResultInterface $result): ?ResultInterface
    {
        $this->design->setArea(Area::AREA_FRONTEND);
        $this->design->setDesignTheme($this->currentTheme);

        return $result;
    }
}
