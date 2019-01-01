<?php

namespace ClawRock\Debug\Plugin\ErrorHandler;

use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WhoopsPlugin
{
    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Whoops\RunFactory
     */
    private $whoopsFactory;

    /**
     * @var \Whoops\Handler\PrettyPageHandlerFactory
     */
    private $prettyPageHandlerFactory;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \Whoops\RunFactory $whoopsFactory,
        \Whoops\Handler\PrettyPageHandlerFactory $prettyPageHandlerFactory
    ) {
        $this->config = $config;
        $this->whoopsFactory = $whoopsFactory;
        $this->prettyPageHandlerFactory = $prettyPageHandlerFactory;
    }

    public function beforeCatchException(Http $subject, Bootstrap $bootstrap, \Exception $exception)
    {
        if ($this->config->getErrorHandler() === ErrorHandler::WHOOPS) {
            $whoops = $this->whoopsFactory->create();
            $whoops->pushHandler($this->prettyPageHandlerFactory->create());
            $whoops->handleException($exception);
        }

        return [$bootstrap, $exception];
    }
}
