<?php

namespace ClawRock\Debug\Test\Unit\Observer\Config;

use ClawRock\Debug\Helper\Config;
use ClawRock\Debug\Observer\Config\DatabaseProfiler;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DatabaseProfilerTest extends TestCase
{
    private $messageManagerMock;

    private $dbProfilerWriterMock;

    private $configMock;

    private $observerMock;

    private $observer;

    protected function setUp()
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

        $this->observer = (new ObjectManager($this))->getObject(DatabaseProfiler::class, [
            'messageManager' => $this->messageManagerMock,
            'dbProfilerWriter' => $this->dbProfilerWriterMock,
            'config' => $this->configMock,
        ]);
    }

    public function testExecuteIndependentConfig()
    {
        $this->observerMock->expects($this->once())->method('getChangedPaths')->willReturn(['not/related/to/database']);
        $this->assertNull($this->observer->execute($this->observerMock));
    }

    public function testExecute()
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

    public function testExecuteException()
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
