<?php

namespace ClawRock\Debug\Model\DataCollector;

class AjaxDataCollector extends AbstractDataCollector
{
    const NAME = 'ajax';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    ) {
        // Done in frontend
        return $this;
    }

    public function isEnabled()
    {
        return $this->helper->isAjaxDataCollectorEnabled();
    }
}
