<?php

namespace ClawRock\Debug\Block\Profiler;

use Magento\Framework\View\Element\Template;

class Search extends Template
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $token;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->registry = $registry;
    }

    public function paramSelected($param, $expected)
    {
        if ($this->request->getParam($param) === $expected) {
            return 'selected="selected"';
        }

        return '';
    }

    public function getParam($param)
    {
        return $this->request->getParam($param);
    }

    public function getLimits()
    {
        return [10, 50, 100];
    }

    public function getMethods()
    {
        return ['DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'];
    }

    public function getToken()
    {
        if ($this->token === null) {
            $this->token = $this->registry->registry('current_profile')->getToken();
        }

        return $this->token;
    }

    public function isExpanded()
    {
        return $this->getData('expanded');
    }
}
