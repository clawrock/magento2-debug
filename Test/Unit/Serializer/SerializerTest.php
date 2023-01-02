<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Serializer;

use ClawRock\Debug\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    /**
     * @param mixed $input
     * @return void
     * @dataProvider serializationProvider
     */
    public function testSerialization($input): void
    {
        $serializer = new Serializer();

        $this->assertEquals($input, $serializer->unserialize($serializer->serialize($input)));
    }

    public function serializationProvider(): array
    {
        return [
            ['1', 2, 3],
            [new \stdClass()],
            ['<pre>some html</pre>'],
            [['foo' => 'bar', new \stdClass(), 'Hello', 'double' => 2.44]],
        ];
    }
}
