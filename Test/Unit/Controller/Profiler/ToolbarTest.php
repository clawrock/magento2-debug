<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Toolbar;
use ClawRock\Debug\Model\Profiler;
use ClawRock\Debug\Model\Storage\ProfileMemoryStorage;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class ToolbarTest extends TestCase
{
    private $resultFactoryMock;

    private $resultMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    private $contextMock;

    private $profileRepositoryMock;

    private $profileMock;

    private $profileMemoryStorageMock;

    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);

        $this->profileRepositoryMock = $this->getMockForAbstractClass(ProfileRepositoryInterface::class);

        $this->profileMock = $this->getMockForAbstractClass(ProfileInterface::class);

        $this->profileMemoryStorageMock = $this->getMockBuilder(ProfileMemoryStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = (new ObjectManager($this))->getObject(Toolbar::class, [
            'context' => $this->contextMock,
            'profileRepository' => $this->profileRepositoryMock,
            'profileMemoryStorage' => $this->profileMemoryStorageMock,
        ]);
    }

    public function testExecute()
    {
        $token = 'token';
        $this->requestMock->expects($this->once())->method('getParam')
            ->with(Profiler::URL_TOKEN_PARAMETER)
            ->willReturn($token);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with($token)
            ->willReturn($this->profileMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
