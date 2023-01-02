<?php
declare(strict_types=1);

namespace ClawRock\Debug\Serializer;

interface SerializerInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data): string;

    /**
     * @param string $data
     * @return mixed
     */
    public function unserialize(string $data);
}
