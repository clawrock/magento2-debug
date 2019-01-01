<?php

namespace ClawRock\Debug\Model\Collector;

interface LateCollectorInterface
{
    public function lateCollect(): LateCollectorInterface;
}
