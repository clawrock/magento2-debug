<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Controller\Profiler\PHPInfo;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class PHPInfoTest extends TestCase
{
    public function testExecute()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultMock = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->expects($this->once())->method('getResultFactory')->willReturn($resultFactoryMock);

        $resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_RAW)
            ->willReturn($resultMock);

        $controller = (new ObjectManager($this))->getObject(PHPInfo::class, [
            'context' => $contextMock,
        ]);

        ob_start();
        $result = $controller->execute();
        ob_end_clean();

        $this->assertInstanceOf(ResultInterface::class, $result);
    }
}
