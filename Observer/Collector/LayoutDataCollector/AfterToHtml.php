<?php

namespace ClawRock\Debug\Observer\Collector\LayoutDataCollector;

class AfterToHtml implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\LayoutDataCollector
     */
    private $dataCollector;

    public function __construct(
        \ClawRock\Debug\Model\DataCollector\LayoutDataCollector $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $observer->getBlock();
        $id = $block->getData($this->dataCollector::BLOCK_PROFILER_ID_KEY);
        $data = $this->dataCollector->getBlockData($id);

        $data += $this->extractBlockData($block);
        $data['stop_render'] = microtime(true);
        $data['render_time'] = $data['stop_render'] - $data['start_render'];

        if ($block instanceof \Magento\Framework\View\Element\Template) {
            $data['template'] = $block->getTemplate();
        }

        $this->dataCollector->setBlockData($id, $data);
        $this->dataCollector->logBlock($id, $block);
    }

    protected function extractBlockData(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        return [
            'name'   => $block->getNameInLayout(),
            'class'  => get_class($block),
            'module' => $block->getModuleName(),
        ];
    }
}
