<?php
declare(strict_types=1);

namespace ClawRock\Debug\Serializer;

class Serializer implements SerializerInterface
{
    public function serialize($data): string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged,Magento2.Security.InsecureFunction.FoundWithAlternative
        return (string) gzcompress(serialize($data), 9);
    }

    public function unserialize(string $data)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged,Magento2.Security.InsecureFunction.FoundWithAlternative
        return unserialize((string) gzuncompress($data));
    }
}
