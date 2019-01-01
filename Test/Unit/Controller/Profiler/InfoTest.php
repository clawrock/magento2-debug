<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Info;
use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Profiler;
use ClawRock\Debug\Model\Storage\ProfileMemoryStorage;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    private $resultMock;

    private $resultFactoryMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    private $contextMock;

    private $layoutMock;

    private $profileRepositoryMock;

    private $profileMemoryStorageMock;

    private $profileMock;

    private $collectorMock;

    private $blockMock;

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

        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $this->profileRepositoryMock = $this->getMockForAbstractClass(ProfileRepositoryInterface::class);

        $this->profileMemoryStorageMock = $this->getMockBuilder(ProfileMemoryStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileMock = $this->getMockForAbstractClass(ProfileInterface::class);

        $this->collectorMock = $this->getMockForAbstractClass(CollectorInterface::class);

        $this->blockMock = $this->getMockBuilder(Template::class)
            ->setMethods(['setCollector'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = (new ObjectManager($this))->getObject(Info::class, [
            'context' => $this->contextMock,
            'layout' => $this->layoutMock,
            'profileRepository' => $this->profileRepositoryMock,
            'profileMemoryStorage' => $this->profileMemoryStorageMock,
        ]);
    }

    public function testExecute()
    {
        $token = 'token';
        $panel = 'panel';
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls($token, $panel);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with($token)
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with($panel)
            ->willReturn(true);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with($panel)
            ->willReturn($this->collectorMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => $panel,
            'profiler' => 'info',
        ], 'debug');

        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('setCollector')
            ->with($this->collectorMock);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteLatest()
    {
        $token = 'latest';
        $panel = 'panel';
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls($token, $panel);
        $this->profileRepositoryMock->expects($this->once())->method('findLatest')
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with($panel)
            ->willReturn(true);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with($panel)
            ->willReturn($this->collectorMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => $panel,
            'profiler' => 'info',
        ], 'debug');

        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('setCollector')
            ->with($this->collectorMock);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteNoCollector()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $token = 'token';
        $panel = 'panel';
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls($token, $panel);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with($token)
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with($panel)
            ->willReturn(false);

        $this->controller->execute();
    }

    public function testExecuteNoPanelBlock()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $token = 'token';
        $panel = 'panel';
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls($token, $panel);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with($token)
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with($panel)
            ->willReturn(true);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with($panel)
            ->willReturn($this->collectorMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => $panel,
            'profiler' => 'info',
        ], 'debug');

        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn(false);

        $this->controller->execute();
    }
}
