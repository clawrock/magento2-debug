<?php

namespace ClawRock\Debug\Block\Profiler;

use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use Symfony\Component\VarDumper\VarDumper;

class Collector extends \ClawRock\Debug\Block\Profiler
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\DataCollectorInterface
     */
    protected $collector;

    /**
     * @var \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer
     */
    protected $tableRenderer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $data);
        $this->tableRenderer = $tableRenderer;
    }

    public function setCollector(DataCollectorInterface $collector)
    {
        $this->collector = $collector;

        return $this;
    }

    public function getCollector()
    {
        return $this->collector;
    }

    public function renderTable($data, $labels = null)
    {
        return $this->tableRenderer->render(['items' => $data, 'labels' => $labels]);
    }

    public function dump($value)
    {
        return VarDumper::dump($value);
    }
}
