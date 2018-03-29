<?php

namespace ClawRock\Debug\Block\Profiler\Renderer;

use Magento\Framework\View\Element\Template;
use Symfony\Component\VarDumper\VarDumper;

class DefaultRenderer extends Template
{
    public function render(array $data = [])
    {
        $this->setData($data);
        $output = $this->_toHtml();
        $this->unsetData();

        return $output;
    }

    public function dump($value)
    {
        return VarDumper::dump($value);
    }
}
