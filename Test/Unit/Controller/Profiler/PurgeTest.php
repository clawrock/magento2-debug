<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Controller\Profiler\Purge;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

class PurgeTest extends TestCase
{
    /** @var \Magento\Framework\Controller\ResultFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\ResultFactory $resultFactoryMock;
    /** @var \Magento\Framework\App\Response\RedirectInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\Response\RedirectInterface $responseRedirectMock;
    /** @var \Magento\Framework\Controller\Result\Redirect&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\Result\Redirect $redirectMock;
    /** @var \ClawRock\Debug\Model\Storage\ProfileFileStorage&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Storage\ProfileFileStorage $profileFileStorageMock;
    /** @var \ClawRock\Debug\Logger\Logger&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Logger\Logger $loggerMock;
    private \ClawRock\Debug\Controller\Profiler\Purge $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseRedirectMock = $this->createMock(\Magento\Framework\App\Response\RedirectInterface::class);

        $this->redirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->setMethods(['getRefererUrl', 'setUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileFileStorageMock = $this->getMockBuilder(\ClawRock\Debug\Model\Storage\ProfileFileStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\ClawRock\Debug\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new Purge(
            $this->resultFactoryMock,
            $this->responseRedirectMock,
            $this->profileFileStorageMock,
            $this->loggerMock
        );
    }

    public function testExecute(): void
    {
        $referer = 'referer';
        $this->profileFileStorageMock->expects($this->once())->method('purge');
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);
        $this->responseRedirectMock->expects($this->once())->method('getRefererUrl')->willReturn($referer);
        $this->redirectMock->expects($this->once())->method('setUrl')->with($referer)->willReturnSelf();

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteException(): void
    {
        $referer = 'referer';
        $exception = new FileSystemException(new Phrase('exception'));
        $this->profileFileStorageMock->expects($this->once())->method('purge')->willThrowException($exception);
        $this->loggerMock->expects($this->once())->method('error');
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);
        $this->responseRedirectMock->expects($this->once())->method('getRefererUrl')->willReturn($referer);
        $this->redirectMock->expects($this->once())->method('setUrl')->with($referer)->willReturnSelf();

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
