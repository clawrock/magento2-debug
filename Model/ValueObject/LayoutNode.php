<?php

namespace ClawRock\Debug\Model\ValueObject;

class LayoutNode
{
    /**
     * @var \ClawRock\Debug\Model\ValueObject\Block
     */
    private $block;

    /**
     * @var float
     */
    private $layoutRenderTime;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $children;

    public function __construct(
        \ClawRock\Debug\Model\ValueObject\Block $block,
        float $layoutRenderTime = null,
        string $prefix = null,
        array $children
    ) {
        $this->block = $block;
        $this->layoutRenderTime = $layoutRenderTime;
        $this->prefix = $prefix;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->block->getName();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->block->getClass();
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->block->getModule();
    }

    /**
     * @return float
     */
    public function getRenderTime(): float
    {
        return $this->block->getRenderTime();
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return (string) $this->prefix;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->block->getTemplate();
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * @return string
     */
    public function getParentId(): string
    {
        return (string) $this->block->getParentId();
    }

    public function getRenderPercent(): float
    {
        if (empty($this->layoutRenderTime)) {
            return 0;
        }

        return $this->getRenderTime() / $this->layoutRenderTime;
    }
}
