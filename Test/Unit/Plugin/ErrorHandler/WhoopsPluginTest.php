<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\ErrorHandler;

use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use ClawRock\Debug\Plugin\ErrorHandler\WhoopsPlugin;
use PHPUnit\Framework\TestCase;

class WhoopsPluginTest extends TestCase
{
    public function testBeforeCatchException(): void
    {
        $subjectMock = $this->getMockBuilder(\Magento\Framework\App\Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bootstrapMock = $this->getMockBuilder(\Magento\Framework\App\Bootstrap::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $whoopsFactoryMock = $this->getMockBuilder(\Whoops\RunFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $whoopsMock = $this->getMockForAbstractClass(\Whoops\RunInterface::class);

        $prettyPageHandlerFactoryMock = $this->getMockBuilder(\Whoops\Handler\PrettyPageHandlerFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $prettyPageHandlerMock = $this->getMockBuilder(\Whoops\Handler\PrettyPageHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \Exception();

        $configMock->expects($this->once())->method('getErrorHandler')->willReturn(ErrorHandler::WHOOPS);
        $whoopsFactoryMock->expects($this->once())->method('create')->willReturn($whoopsMock);
        $prettyPageHandlerFactoryMock->expects($this->once())->method('create')->willReturn($prettyPageHandlerMock);
        $whoopsMock->expects($this->once())->method('pushHandler')->with($prettyPageHandlerMock);
        $whoopsMock->expects($this->once())->method('handleException')->with($exception);

        $plugin = new WhoopsPlugin($configMock, $whoopsFactoryMock, $prettyPageHandlerFactoryMock);

        $this->assertEquals([
            $bootstrapMock, $exception,
        ], $plugin->beforeCatchException($subjectMock, $bootstrapMock, $exception));
    }
}
