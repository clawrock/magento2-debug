<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Logger;

use ClawRock\Debug\Logger\DataLogger;
use ClawRock\Debug\Logger\LoggableInterface;
use PHPUnit\Framework\TestCase;

class DataLoggerTest extends TestCase
{
    /** @var \ClawRock\Debug\Logger\LoggableInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Logger\LoggableInterface $loggableMock;
    private \ClawRock\Debug\Logger\DataLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggableMock = $this->getMockForAbstractClass(LoggableInterface::class);
        $this->logger = new DataLogger();
    }

    public function testGetLogs(): void
    {
        $this->assertEquals([], $this->logger->getLogs());
    }

    public function testLog(): void
    {
        $this->loggableMock->expects($this->once())->method('getId')->willReturn('ID');
        $this->assertInstanceOf(DataLogger::class, $this->logger->log($this->loggableMock));
        $this->assertEquals(['ID' => $this->loggableMock], $this->logger->getLogs());
    }
}
