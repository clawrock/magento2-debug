<?php

namespace ClawRock\Debug\Model\DataCollector;

class AjaxDataCollector extends AbstractDataCollector
{
    const NAME = 'ajax';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        // Done in frontend
        return $this;
    }

    public function isEnabled()
    {
        return $this->helper->isAjaxDataCollectorEnabled();
    }
}
