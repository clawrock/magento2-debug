<?php

namespace ClawRock\Debug\Block\Profiler\Renderer;

class TableRenderer extends DefaultRenderer
{
    protected function _construct()
    {
        $this->setData('template', 'ClawRock_Debug::profiler/renderer/table.phtml');
        parent::_construct();
    }

    public function getClass()
    {
        return (string) ($this->_data['class'] ?? '');
    }

    public function getLabels()
    {
        return $this->_data['labels'] ?? [];
    }

    public function getItems()
    {
        return $this->_data['items'] ?? [];
    }
}
