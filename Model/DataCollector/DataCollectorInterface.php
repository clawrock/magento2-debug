<?php

namespace ClawRock\Debug\Model\DataCollector;

interface DataCollectorInterface
{
    const COLLECTOR_PLACEHOLDER = 'debug.toolbar.collectors.%s';

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    );

    public function getCollectorName();

    public function getBlockName();

    public function isEnabled();
}
