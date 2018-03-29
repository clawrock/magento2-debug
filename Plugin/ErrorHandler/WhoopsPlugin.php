<?php

namespace ClawRock\Debug\Plugin\ErrorHandler;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

class WhoopsPlugin
{
    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    protected $helper;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper
    ) {
        $this->helper = $helper;
    }

    public function beforeCatchException(Http $subject, Bootstrap $bootstrap, \Exception $exception)
    {
        if ($this->helper->isWhoopsEnabled()) {
            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->handleException($exception);
        }

        return [$bootstrap, $exception];
    }
}
