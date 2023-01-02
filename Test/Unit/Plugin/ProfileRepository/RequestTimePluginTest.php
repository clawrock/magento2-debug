<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\ProfileRepository;

use ClawRock\Debug\Model\Collector\TimeCollector;
use ClawRock\Debug\Plugin\ProfileRepository\RequestTimePlugin;
use PHPUnit\Framework\TestCase;

class RequestTimePluginTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Collector\TimeCollector&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\TimeCollector $timeCollectorMock;
    /** @var \ClawRock\Debug\Api\Data\ProfileInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\Data\ProfileInterface $profileMock;
    /** @var \ClawRock\Debug\Api\ProfileRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $subjectMock;
    private \ClawRock\Debug\Plugin\ProfileRepository\RequestTimePlugin $plugin;

    protected function setUp(): void
    {
        $this->timeCollectorMock = $this->createMock(\ClawRock\Debug\Model\Collector\TimeCollector::class);
        $this->profileMock = $this->createMock(\ClawRock\Debug\Api\Data\ProfileInterface::class);
        $this->subjectMock = $this->createMock(\ClawRock\Debug\Api\ProfileRepositoryInterface::class);
        $this->plugin = new RequestTimePlugin();
    }

    public function testBeforeSave(): void
    {
        $this->profileMock->expects($this->once())
            ->method('getCollector')
            ->with(TimeCollector::NAME)
            ->willReturn($this->timeCollectorMock);

        $this->timeCollectorMock->expects($this->once())->method('getDuration')->willReturn('1');
        $this->profileMock->expects($this->once())->method('setRequestTime')->with(1);

        $this->assertEquals([$this->profileMock], $this->plugin->beforeSave($this->subjectMock, $this->profileMock));
    }

    public function testBeforeSaveException(): void
    {
        $this->profileMock->expects($this->once())
            ->method('getCollector')
            ->willThrowException(new \InvalidArgumentException());

        $this->profileMock->expects($this->never())->method('setRequestTime');

        $this->assertEquals([$this->profileMock], $this->plugin->beforeSave($this->subjectMock, $this->profileMock));
    }
}
