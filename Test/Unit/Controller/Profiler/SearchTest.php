<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Search;
use ClawRock\Debug\Model\Profile\Criteria;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private $resultFactoryMock;

    private $redirectMock;

    private $resultMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    private $responseMock;

    private $contextMock;

    private $layoutMock;

    private $profileRepositoryMock;

    private $blockMock;

    private $profileMock;

    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->redirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['getRefererUrl', 'setUrl', 'redirect'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->responseMock = $this->getMockForAbstractClass(ResponseInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->once())->method('getResponse')->willReturn($this->responseMock);

        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $this->profileRepositoryMock = $this->getMockForAbstractClass(ProfileRepositoryInterface::class);

        $this->blockMock = $this->getMockBuilder(Template::class)
            ->setMethods(['addData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileMock = $this->getMockForAbstractClass(ProfileInterface::class);

        $this->controller = (new ObjectManager($this))->getObject(Search::class, [
            'context' => $this->contextMock,
            'layout' => $this->layoutMock,
            'profileRepository' => $this->profileRepositoryMock,
        ]);
    }

    public function testExecute()
    {
        $token = null;
        $ip = '127.0.0.1';
        $url = '/';
        $limit = '50';
        $method = 'GET';
        $start = null;
        $end = null;
        $statusCode = '200';
        $this->requestMock->expects($this->exactly(8))->method('getParam')
            ->withConsecutive(['_token'], ['ip'], ['url'], ['limit'], ['method'], ['start'], ['end'], ['status_code'])
            ->willReturnOnConsecutiveCalls($token, $ip, $url, $limit, $method, $start, $end, $statusCode);

        $this->resultFactoryMock->expects($this->once())->method('create')->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultMock);
        $this->resultMock->expects($this->once())->method('addPageLayoutHandles')->with([
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

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }

    public function testExecuteToken()
    {
        $token = 'token';
        $this->requestMock->expects($this->once())->method('getParam')->with('_token')->willReturn($token);
        $this->redirectMock->expects($this->once())->method('redirect')
            ->with($this->responseMock, '_debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);

        $this->assertEquals($this->responseMock, $this->controller->execute());
    }
}
