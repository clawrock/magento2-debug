<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\ErrorHandler;

use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

class WhoopsPlugin
{
    private \ClawRock\Debug\Helper\Config $config;
    private \Whoops\RunFactory $whoopsFactory;
    private \Whoops\Handler\PrettyPageHandlerFactory $prettyPageHandlerFactory;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \Whoops\RunFactory $whoopsFactory,
        \Whoops\Handler\PrettyPageHandlerFactory $prettyPageHandlerFactory
    ) {
        $this->config = $config;
        $this->whoopsFactory = $whoopsFactory;
        $this->prettyPageHandlerFactory = $prettyPageHandlerFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Framework\App\Http $subject
     * @param \Magento\Framework\App\Bootstrap $bootstrap
     * @param \Exception $exception
     * @return array
     */
    public function beforeCatchException(Http $subject, Bootstrap $bootstrap, \Exception $exception): array
    {
        if ($this->config->getErrorHandler() === ErrorHandler::WHOOPS) {
            $whoops = $this->whoopsFactory->create();
            $whoops->pushHandler($this->prettyPageHandlerFactory->create());
            $whoops->handleException($exception);
        }

        return [$bootstrap, $exception];
    }
}
