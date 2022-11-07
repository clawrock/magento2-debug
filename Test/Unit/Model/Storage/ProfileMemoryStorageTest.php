<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\Storage;

use ClawRock\Debug\Model\Storage\ProfileMemoryStorage;
use PHPUnit\Framework\TestCase;

class ProfileMemoryStorageTest extends TestCase
{
    public function testStorage(): void
    {
        $profileMock = $this->getMockForAbstractClass(\ClawRock\Debug\Api\Data\ProfileInterface::class);

        $storage = new ProfileMemoryStorage();
        $storage->write($profileMock);
        $this->assertEquals($profileMock, $storage->read());
    }
}
