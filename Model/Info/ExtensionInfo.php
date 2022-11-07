<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

class ExtensionInfo
{
    public function isXdebugEnabled(): bool
    {
        return extension_loaded('xdebug');
    }

    public function isEAcceleratorEnabled(): bool
    {
        return extension_loaded('eaccelerator') && ini_get('eaccelerator.enable');
    }

    public function isApcEnabled(): bool
    {
        return extension_loaded('apc') && ini_get('apc.enabled');
    }

    public function isXCacheEnabled(): bool
    {
        return extension_loaded('xcache') && ini_get('xcache.cacher');
    }

    public function isWinCacheEnabled(): bool
    {
        return extension_loaded('wincache') && ini_get('wincache.ocenabled');
    }

    public function isZendOpcacheEnabled(): bool
    {
        return extension_loaded('Zend OPcache') && ini_get('opcache.enable');
    }
}
