<?php

namespace ClawRock\Debug\Serializer;

class Serializer implements SerializerInterface
{
    public function serialize($data): string
    {
        return gzcompress(serialize($data), 9);
    }

    public function unserialize(string $data)
    {
        return unserialize(gzuncompress($data));
    }
}
