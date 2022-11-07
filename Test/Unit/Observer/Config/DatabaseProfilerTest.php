<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer\Config;

use ClawRock\Debug\Helper\Config;
use ClawRock\Debug\Observer\Config\DatabaseProfiler;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

class DatabaseProfilerTest extends TestCase
{
    /** @var \Magento\Framework\Message\ManagerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Message\ManagerInterface $messageManagerMock;
    /** @var \ClawRock\Debug\Model\Config\Database\ProfilerWriter&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Config\Database\ProfilerWriter $dbProfilerWriterMock;
    /** @var \ClawRock\Debug\Helper\Config&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Config $configMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    private \ClawRock\Debug\Observer\Config\DatabaseProfiler $observer;

    protected function setUp(): void
    {
        $this->messageManagerMock = $this->getMockForAbstractClass(\Magento\Framework\Message\ManagerInterface::class);

        $this->dbProfilerWriterMock = $this->getMockBuilder(\ClawRock\Debug\Model\Config\Database\ProfilerWriter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getChangedPaths'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new DatabaseProfiler(
            $this->messageManagerMock,
            $this->dbProfilerWriterMock,
            $this->configMock
        );
    }

    public function testExecuteIndependentConfig(): void
    {
        $this->observerMock->expects($this->once())->method('getChangedPaths')->willReturn(['not/related/to/database']);
        $this->observer->execute($this->observerMock);
    }

    public function testExecute(): void
    {
        $this->observerMock->expects($this->once())
            ->method('getChangedPaths')
            ->willReturn([Config::CONFIG_COLLECTOR_DATABASE]);

        $this->configMock->expects($this->once())
            ->method('isDatabaseCollectorEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $this->dbProfilerWriterMock->expects($this->once())
            ->method('save')
            ->with(true);

        $this->messageManagerMock->expects($this->never())
            ->method('addExceptionMessage');

        $this->observer->execute($this->observerMock);
    }

    public function testExecuteException(): void
    {
        $this->observerMock->expects($this->once())
            ->method('getChangedPaths')
            ->willReturn([Config::CONFIG_COLLECTOR_DATABASE]);

        $this->configMock->expects($this->once())
            ->method('isDatabaseCollectorEnabled')
            ->willReturn(true);

        $this->configMock->expects($this->once())
            ->method('isActive')
            ->willReturn(false);

        $exception = new FileSystemException(new Phrase('Error'));

        $this->dbProfilerWriterMock->expects($this->once())
            ->method('save')
            ->with(false)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception);

        $this->observer->execute($this->observerMock);
    }
}
