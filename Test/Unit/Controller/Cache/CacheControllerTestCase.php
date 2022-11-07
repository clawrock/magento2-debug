<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Cache;

class CacheControllerTestCase extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\Framework\Controller\ResultInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected \Magento\Framework\Controller\ResultInterface $resultMock;
    /** @var \Magento\Framework\Controller\ResultFactory&\PHPUnit\Framework\MockObject\MockObject */
    protected \Magento\Framework\Controller\ResultFactory $resultFactoryMock;
    /** @var \Magento\Framework\App\RequestInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected \Magento\Framework\App\RequestInterface $requestMock;
    /** @var \Magento\Framework\App\Cache\Manager&\PHPUnit\Framework\MockObject\MockObject */
    protected \Magento\Framework\App\Cache\Manager $cacheManagerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultFactoryMock = $this->createMock(\Magento\Framework\Controller\ResultFactory::class);
        $this->resultMock = $this->createMock(\Magento\Framework\Controller\ResultInterface::class);
        $this->requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $this->cacheManagerMock = $this->createMock(\Magento\Framework\App\Cache\Manager::class);
    }
}
