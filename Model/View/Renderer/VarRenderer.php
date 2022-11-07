<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class VarRenderer
{
    private \Symfony\Component\VarDumper\Cloner\VarCloner $cloner;
    private \Symfony\Component\VarDumper\Dumper\HtmlDumper $dumper;

    public function __construct()
    {
        $this->cloner = new VarCloner();
        $this->dumper = new HtmlDumper();
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function render($value): string
    {
        return (string) $this->dumper->dump($this->cloner->cloneVar($value), true);
    }
}
