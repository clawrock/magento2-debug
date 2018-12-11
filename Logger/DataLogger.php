<?php

namespace ClawRock\Debug\Logger;

class DataLogger
{
    private $data = [];

    public function log(LoggableInterface $value): DataLogger
    {
        $this->data[$value->getId()] = $value;

        return $this;
    }

    public function getLogs()
    {
        return $this->data;
    }
}
