<?php

namespace ClawRock\Debug\Model\DataCollector;

interface DataCollectorInterface
{
    const COLLECTOR_PLACEHOLDER = 'debug.toolbar.collectors.%s';

    /**
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    );

    public function getCollectorName();

    public function getBlockName();

    public function isEnabled();
}
