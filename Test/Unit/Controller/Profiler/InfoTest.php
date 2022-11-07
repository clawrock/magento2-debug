<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Info;
use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Profiler;
use ClawRock\Debug\Model\Storage\ProfileMemoryStorage;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    private const TOKEN_PARAM = 'token';
    private const PANEL_PARAM = 'panel';

    /** @var \Magento\Framework\View\Result\Page&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Result\Page $resultMock;
    /** @var \Magento\Framework\Controller\ResultFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\ResultFactory $resultFactoryMock;
    /** @var \Magento\Framework\App\RequestInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\RequestInterface $requestMock;
    /** @var \Magento\Framework\View\LayoutInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\LayoutInterface $layoutMock;
    /** @var \ClawRock\Debug\Api\ProfileRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepositoryMock;
    /** @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorageMock;
    /** @var \ClawRock\Debug\Api\Data\ProfileInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\Data\ProfileInterface $profileMock;
    /** @var \ClawRock\Debug\Model\Collector\CollectorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\CollectorInterface $collectorMock;
    /** @var \Magento\Framework\View\Element\Template&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Element\Template $blockMock;
    private \ClawRock\Debug\Controller\Profiler\Info $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

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

        $this->controller = new Info(
            $this->resultFactoryMock,
            $this->requestMock,
            $this->layoutMock,
            $this->profileRepositoryMock,
            $this->profileMemoryStorageMock
        );
    }

    public function testExecute(): void
    {
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls(self::TOKEN_PARAM, self::PANEL_PARAM);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with(self::TOKEN_PARAM)
            ->willReturn($this->profileMock);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn(true);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn($this->collectorMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => self::PANEL_PARAM,
            'profiler' => 'info',
        ], 'debug');
        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('setCollector')
            ->with($this->collectorMock);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteLatest(): void
    {
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls('latest', self::PANEL_PARAM);
        $this->profileRepositoryMock->expects($this->once())->method('findLatest')
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn(true);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn($this->collectorMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => self::PANEL_PARAM,
            'profiler' => 'info',
        ], 'debug');
        $this->blockMock->expects($this->once())->method('setCollector')
            ->with($this->collectorMock);
        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn($this->blockMock);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteNoCollector(): void
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls(self::TOKEN_PARAM, self::PANEL_PARAM);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with(self::TOKEN_PARAM)
            ->willReturn($this->profileMock);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn(false);

        $this->controller->execute();
    }

    public function testExecuteNoPanelBlock(): void
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive([Profiler::URL_TOKEN_PARAMETER, ''], ['panel', 'request'])
            ->willReturnOnConsecutiveCalls(self::TOKEN_PARAM, self::PANEL_PARAM);
        $this->profileMock->expects($this->once())->method('hasCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn(true);
        $this->profileRepositoryMock->expects($this->once())->method('getById')
            ->with(self::TOKEN_PARAM)
            ->willReturn($this->profileMock);
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->profileMemoryStorageMock->expects($this->once())->method('write')
            ->with($this->profileMock);
        $this->profileMock->expects($this->once())->method('getCollector')
            ->with(self::PANEL_PARAM)
            ->willReturn($this->collectorMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'panel' => self::PANEL_PARAM,
            'profiler' => 'info',
        ], 'debug');

        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn(false);

        $this->controller->execute();
    }
}
