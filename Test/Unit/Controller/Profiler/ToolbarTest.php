<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Api\ProfileRepositoryInterface;
use ClawRock\Debug\Controller\Profiler\Toolbar;
use ClawRock\Debug\Model\Profiler;
use ClawRock\Debug\Model\Storage\ProfileMemoryStorage;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;

class ToolbarTest extends TestCase
{
    /** @var \Magento\Framework\Controller\ResultFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\ResultFactory $resultFactoryMock;
    /** @var \Magento\Framework\View\Result\Page&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Result\Page $resultMock;
    /** @var \Magento\Framework\App\RequestInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\RequestInterface $requestMock;
    /** @var \ClawRock\Debug\Api\ProfileRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepositoryMock;
    /** @var \ClawRock\Debug\Api\Data\ProfileInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\Data\ProfileInterface $profileMock;
    /** @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorageMock;
    private \ClawRock\Debug\Controller\Profiler\Toolbar $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(\Magento\Framework\App\RequestInterface::class);

        $this->profileRepositoryMock = $this->getMockForAbstractClass(ProfileRepositoryInterface::class);

        $this->profileMock = $this->getMockForAbstractClass(ProfileInterface::class);

        $this->profileMemoryStorageMock = $this->getMockBuilder(ProfileMemoryStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new Toolbar(
            $this->resultFactoryMock,
            $this->requestMock,
            $this->profileMemoryStorageMock,
            $this->profileRepositoryMock
        );
    }

    public function testExecute(): void
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
