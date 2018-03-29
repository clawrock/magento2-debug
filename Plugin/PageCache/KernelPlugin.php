<?php

namespace ClawRock\Debug\Plugin\PageCache;

class KernelPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterLoad(\Magento\Framework\App\PageCache\Kernel $subject, $result)
    {
        if ($result !== false) {
            $this->registry->register('debug_fpc_request', true);
        }

        return $result;
    }
}
