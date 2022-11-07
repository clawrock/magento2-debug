<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class LayoutNode
{
    private \ClawRock\Debug\Model\ValueObject\Block $block;
    private ?float $layoutRenderTime;
    private ?string $prefix;
    private array $children;

    public function __construct(
        \ClawRock\Debug\Model\ValueObject\Block $block,
        ?float $layoutRenderTime = null,
        ?string $prefix = null,
        array $children = []
    ) {
        $this->block = $block;
        $this->layoutRenderTime = $layoutRenderTime;
        $this->prefix = $prefix;
        $this->children = $children;
    }

    public function getName(): string
    {
        return $this->block->getName();
    }

    public function getClass(): string
    {
        return $this->block->getClass();
    }

    public function getModule(): string
    {
        return $this->block->getModule();
    }

    public function getRenderTime(): float
    {
        return $this->block->getRenderTime();
    }

    public function getPrefix(): string
    {
        return (string) $this->prefix;
    }

    public function getTemplate(): string
    {
        return $this->block->getTemplate();
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function getParentId(): string
    {
        return (string) $this->block->getParentId();
    }

    public function getRenderPercent(): float
    {
        if (empty($this->layoutRenderTime)) {
            return 0.0;
        }

        return $this->getRenderTime() / $this->layoutRenderTime;
    }
}
