<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

interface LateCollectorInterface
{
    public function lateCollect(): LateCollectorInterface;
}
