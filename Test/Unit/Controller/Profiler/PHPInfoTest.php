<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Controller\Profiler\PHPInfo;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use PHPUnit\Framework\TestCase;

class PHPInfoTest extends TestCase
{
    public function testExecute(): void
    {
        $resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultMock = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_RAW)
            ->willReturn($resultMock);

        $controller = new PHPInfo($resultFactoryMock);

        ob_start();
        $result = $controller->execute();
        ob_end_clean();

        $this->assertInstanceOf(ResultInterface::class, $result);
    }
}
