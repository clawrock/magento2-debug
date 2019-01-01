<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Controller\Profiler\Purge;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class PurgeTest extends TestCase
{
    private $resultFactoryMock;

    private $redirectMock;

    private $contextMock;

    private $profileFileStorageMock;

    private $loggerMock;

    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->redirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->setMethods(['getRefererUrl', 'setUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())->method('getRedirect')->willReturn($this->redirectMock);

        $this->profileFileStorageMock = $this->getMockBuilder(\ClawRock\Debug\Model\Storage\ProfileFileStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\ClawRock\Debug\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = (new ObjectManager($this))->getObject(Purge::class, [
            'context' => $this->contextMock,
            'profileFileStorage' => $this->profileFileStorageMock,
            'logger' => $this->loggerMock,
        ]);
    }

    public function testExecute()
    {
        $referer = 'referer';
        $this->profileFileStorageMock->expects($this->once())->method('purge');
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);
        $this->redirectMock->expects($this->once())->method('getRefererUrl')->willReturn($referer);
        $this->redirectMock->expects($this->once())->method('setUrl')->with($referer)->willReturnSelf();

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteException()
    {
        $referer = 'referer';
        $exception = new FileSystemException(new Phrase('exception'));
        $this->profileFileStorageMock->expects($this->once())->method('purge')->willThrowException($exception);
        $this->loggerMock->expects($this->once())->method('critical')->with($exception);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);
        $this->redirectMock->expects($this->once())->method('getRefererUrl')->willReturn($referer);
        $this->redirectMock->expects($this->once())->method('setUrl')->with($referer)->willReturnSelf();

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
