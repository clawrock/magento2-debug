<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;
use ClawRock\Debug\Model\Collector\LayoutCollector;

class Block implements LoggableInterface
{
    private string $id;
    private string $name;
    private string $class;
    private string $module;
    private float $renderTime;
    private string $template;
    private array $children;
    private string $parentId;

    public function __construct(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        $this->id = (string) $block->getData(LayoutCollector::BLOCK_PROFILER_ID_KEY);
        $this->name = (string) $block->getNameInLayout();
        $this->class = get_class($block);
        $this->module = $block->getModuleName();
        $this->renderTime = (float) $block->getData(LayoutCollector::RENDER_TIME);
        $this->template = (string) $block->getTemplate();
        $this->children = $block->getChildNames();
        $this->parentId = $block->getParentBlock() instanceof \Magento\Framework\View\Element\AbstractBlock
            ? (string) $block->getParentBlock()->getData(LayoutCollector::BLOCK_PROFILER_ID_KEY)
            : '';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getRenderTime(): float
    {
        return $this->renderTime;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }
}
