<?php

namespace ClawRock\Debug\Block\Profiler\Renderer;

class CallStackRenderer extends DefaultRenderer
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dir = $dir;
    }

    protected function _construct()
    {
        $this->setData('template', 'ClawRock_Debug::profiler/renderer/callstack.phtml');
        parent::_construct();
    }

    public function getStackId()
    {
        return $this->_data['id'] ?: $this->_data['id'] = uniqid();
    }

    public function getStack()
    {
        return $this->_data['stack'] ?? [];
    }

    public function getFile(array $call)
    {
        if (!isset($call['file'])) {
            return '';
        }

        $file = $call['file'];
        $rootDir = $this->dir->getRoot();
        if (strpos($file, $rootDir) === 0) {
            $file = substr($file, strlen($rootDir));
        }

        return $file . ':' . $call['line'];
    }

    public function getFunction(array $call)
    {
        return ($call['class'] ?? '') . '::' . ($call['function'] ?? '');
    }
}
