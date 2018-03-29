<?php

namespace ClawRock\Debug\Model\DataCollector;

class LayoutDataCollector extends AbstractDataCollector
{
    const NAME = 'layout';

    const BLOCK_PROFILER_ID_KEY = 'profiler_id';

    const HANDLES                   = 'handles';
    const BLOCKS_CREATED_COUNT      = 'blocks_created_count';
    const BLOCKS_RENDERED_COUNT     = 'blocks_rendered_count';
    const BLOCKS_NOT_RENDERED       = 'blocks_not_rendered';
    const BLOCKS_NOT_RENDERED_COUNT = 'blocks_not_rendered_count';
    const BLOCKS_DATA               = 'blocks_data';
    const RENDER_TIME               = 'render_time';

    protected $renderLog = [];

    protected $renderedBlocks = [];

    protected $currentBlock;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var array
     */
    private $blocks = [];

    /**
     * @var array
     */
    private $blocksData = [];

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($helper);

        $this->layout = $layout;
    }

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
        $notRenderedBlocks = $this->collectNotRenderedBlocks();
        $renderTime = $this->collectRenderTime();

        $this->data = [
            self::HANDLES                   => $this->layout->getUpdate()->getHandles(),
            self::BLOCKS_CREATED_COUNT      => count($this->layout->getAllBlocks()),
            self::BLOCKS_RENDERED_COUNT     => count($this->blocks),
            self::BLOCKS_NOT_RENDERED       => $notRenderedBlocks,
            self::BLOCKS_NOT_RENDERED_COUNT => count($notRenderedBlocks),
            self::BLOCKS_DATA               => $this->blocksData,
            self::RENDER_TIME               => $renderTime,
        ];

        return $this;
    }

    public function setBlockData($id, array $data)
    {
        $this->blocksData[$id] = $data;

        return $this;
    }

    public function getBlockData($id)
    {
        return $this->blocksData[$id];
    }

    public function addBlockChildren($id, $childId)
    {
        if (!isset($this->blocksData[$id])) {
            return $this;
        }

        if (!is_array($this->blocksData[$id]['children'])) {
            $this->blocksData[$id]['children'] = [];
        }

        $this->blocksData[$id]['children'][] = $childId;

        return $this;
    }

    public function logBlock($id, \Magento\Framework\View\Element\BlockInterface $block)
    {
        $this->blocks[$id] = $block;

        return $this;
    }

    protected function collectRenderTime()
    {
        $renderTime = 0;

        foreach ($this->blocksData as &$data) {
            if (!isset($data[self::RENDER_TIME])) {
                continue; // Block not rendered
            }
            $blockRenderTime = $data[self::RENDER_TIME];
            foreach ($data['children'] ?? [] as $childId) {
                if (!isset($this->blocksData[$childId])) {
                    continue; // Child data not exists
                }
                $blockRenderTime -= $this->blocksData[$childId][self::RENDER_TIME];
            }
            $data[self::RENDER_TIME] = $blockRenderTime;
            $renderTime += $blockRenderTime;
        }

        return $renderTime * 1000;
    }

    protected function collectNotRenderedBlocks()
    {
        $blocks = [];

        foreach ($this->layout->getAllBlocks() as $block) {
            /** @var \Magento\Framework\View\Element\AbstractBlock $block */
            if (!$block->getData(self::BLOCK_PROFILER_ID_KEY)) {
                $blocks[] = $this->getBaseBlockData($block);
            }
        }

        return $blocks;
    }

    protected function getBaseBlockData(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        return [
            'name'   => $block->getNameInLayout(),
            'class'  => get_class($block),
            'module' => $block->getModuleName(),
        ];
    }

    public function getRenderTime()
    {
        return sprintf('%0.0f', $this->data[self::RENDER_TIME] ?? 0);
    }

    public function getBlocksData()
    {
        return $this->data[self::BLOCKS_DATA] ?? [];
    }

    public function getBlocksNotRendered()
    {
        return $this->data[self::BLOCKS_NOT_RENDERED] ?? [];
    }

    public function getHandles()
    {
        return $this->data[self::HANDLES] ?? [];
    }

    public function countCreatedBlocks()
    {
        return $this->data[self::BLOCKS_CREATED_COUNT] ?? 0;
    }

    public function countRenderedBlocks()
    {
        return $this->data[self::BLOCKS_RENDERED_COUNT] ?? 0;
    }

    public function countNotRenderedBlocks()
    {
        return $this->data[self::BLOCKS_NOT_RENDERED_COUNT] ?? 0;
    }

    public function isEnabled()
    {
        return $this->helper->isLayoutDataCollectorEnabled();
    }
}
