<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

interface RendererInterface
{
    public function render(): string;
}
