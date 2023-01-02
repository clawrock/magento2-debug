<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Search;
use ClawRock\Debug\Model\Profile\Criteria;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    /** @var \Magento\Framework\Controller\ResultFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\ResultFactory $resultFactoryMock;
    /** @var \Magento\Framework\App\RequestInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\RequestInterface $requestMock;
    /** @var \Magento\Framework\View\LayoutInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\LayoutInterface $layoutMock;
    /** @var \ClawRock\Debug\Api\ProfileRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepositoryMock;
    /** @var \Magento\Framework\View\Element\Template&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Element\Template $blockMock;
    /** @var \ClawRock\Debug\Api\Data\ProfileInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\Data\ProfileInterface $profileMock;
    private \ClawRock\Debug\Controller\Profiler\Search $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->profileRepositoryMock = $this->getMockForAbstractClass(ProfileRepositoryInterface::class);

        $this->blockMock = $this->getMockBuilder(Template::class)
            ->setMethods(['addData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileMock = $this->getMockForAbstractClass(ProfileInterface::class);

        $this->controller = new Search(
            $this->resultFactoryMock,
            $this->requestMock,
            $this->layoutMock,
            $this->profileRepositoryMock
        );
    }

    public function testExecute(): void
    {
        $token = null;
        $ip = '127.0.0.1';
        $url = '/';
        $limit = 50;
        $method = 'GET';
        $start = null;
        $end = null;
        $statusCode = 200;
        $this->requestMock->expects($this->exactly(8))->method('getParam')
            ->withConsecutive(['_token'], ['ip'], ['url'], ['limit'], ['method'], ['start'], ['end'], ['status_code'])
            ->willReturnOnConsecutiveCalls($token, $ip, $url, $limit, $method, $start, $end, $statusCode);

        $pageMock = $this->createMock(Page::class);
        $this->resultFactoryMock->expects($this->once())->method('create')->with(ResultFactory::TYPE_PAGE)
            ->willReturn($pageMock);
        $pageMock->expects($this->once())->method('addPageLayoutHandles')->with([
            'profiler' => 'info',
        ], 'debug');
        $this->layoutMock->expects($this->once())->method('getBlock')
            ->with('debug.profiler.panel.content')
            ->willReturn($this->blockMock);
        $this->profileRepositoryMock->expects($this->once())->method('find')
            ->willReturn([$this->profileMock]);
        $this->blockMock->expects($this->once())->method('addData')->with([
            'results' => [$this->profileMock],
            'criteria' => new Criteria($ip, $url, $limit, $method, $start, $end, $statusCode),
        ])->willReturnSelf();

        $this->assertEquals($pageMock, $this->controller->execute());
    }

    public function testExecuteToken(): void
    {
        $token = 'token';
        $redirectMock = $this->createMock(Redirect::class);
        $this->resultFactoryMock->expects($this->once())->method('create')->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($redirectMock);
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('_debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);

        $this->requestMock->expects($this->once())->method('getParam')->with('_token')->willReturn($token);

        $this->assertEquals($redirectMock, $this->controller->execute());
    }
}
