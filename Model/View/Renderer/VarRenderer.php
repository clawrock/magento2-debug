<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Symfony\Component\VarDumper\VarDumper;

class VarRenderer implements RendererInterface
{
    private $variable;

    public function __construct($variable)
    {
        $this->variable = $variable;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return string
     */
    public function render(): string
    {
        return (string) VarDumper::dump($this->variable);
    }
}
