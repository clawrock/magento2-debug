<?php

namespace ClawRock\Debug\Block\Profiler\Renderer;

class ParametersRenderer extends DefaultRenderer
{
    protected function _construct()
    {
        $this->setData('template', 'ClawRock_Debug::profiler/renderer/parameters.phtml');
        parent::_construct();
    }

    public function getLabels()
    {
        return $this->_data['labels'] ?? [];
    }
}
