<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model;

class DataCollector
{
    private array $data = [];

    public function setData(array $data): DataCollector
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData(string $key = '')
    {
        if ($key) {
            return $this->data[$key] ?? null;
        }

        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addData(string $key, $value): DataCollector
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function appendData(string $key, $value): DataCollector
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = [];
        }
        $this->data[$key][] = $value;

        return $this;
    }

    public function removeData(string $key): DataCollector
    {
        unset($this->data[$key]);

        return $this;
    }
}
