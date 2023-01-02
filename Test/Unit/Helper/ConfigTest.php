<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Helper;

use ClawRock\Debug\Helper\Config;
use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use ClawRock\Debug\Model\Storage\HttpStorage;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Phrase;
use Magento\Framework\PhraseFactory;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @var \Magento\Framework\PhraseFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\PhraseFactory $phraseFactoryMock;
    /** @var \Magento\Framework\Phrase&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Phrase $phraseMock;
    /** @var \Magento\Framework\App\State&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\State $appStateMock;
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigMock;
    /** @var \Magento\Framework\App\DeploymentConfig&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\DeploymentConfig $deploymentConfigMock;
    /** @var \ClawRock\Debug\Model\Storage\HttpStorage&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Storage\HttpStorage $httpStorageMock;
    private \ClawRock\Debug\Helper\Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->phraseFactoryMock = $this->getMockBuilder(PhraseFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->phraseMock = $this->getMockBuilder(Phrase::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->appStateMock = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->deploymentConfigMock = $this->getMockBuilder(\Magento\Framework\App\DeploymentConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpStorageMock = $this->getMockBuilder(HttpStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = new Config(
            $this->phraseFactoryMock,
            $this->appStateMock,
            $this->scopeConfigMock,
            $this->deploymentConfigMock,
            $this->httpStorageMock
        );
    }

    public function testGetErrorHandler(): void
    {
        $this->appStateMock->expects($this->once())->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(Config::CONFIG_ERROR_HANDLER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(ErrorHandler::WHOOPS);

        $this->assertEquals(ErrorHandler::WHOOPS, $this->config->getErrorHandler());
    }

    public function testIsDatabaseCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_DATABASE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);

        $this->deploymentConfigMock->expects($this->once())->method('get')
            ->with('db/connection/default/profiler/enabled')
            ->willReturn(true);

        $this->assertTrue($this->config->isDatabaseCollectorEnabled());
    }

    public function testIsAjaxCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_AJAX, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isAjaxCollectorEnabled());
    }

    public function testIsAdminhtmlActive(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_ENABLED_ADMINHTML, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isAdminhtmlActive());
    }

    public function testIsConfigCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_CONFIG, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isConfigCollectorEnabled());
    }

    public function testIsModelCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_MODEL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isModelCollectorEnabled());
    }

    public function testIsPluginCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_PLUGIN, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isPluginCollectorEnabled());
    }

    public function testIsTranslationCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_TRANSLATION, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isTranslationCollectorEnabled());
    }

    public function testGetCollectors(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(Config::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(['collector1', 'collector2']);
        $this->assertEquals(['collector1', 'collector2'], $this->config->getCollectors());
    }

    public function testIsLayoutCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_LAYOUT, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isLayoutCollectorEnabled());
    }

    public function testIsMemoryCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_MEMORY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isMemoryCollectorEnabled());
    }

    public function testIsCustomerCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_CUSTOMER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isCustomerCollectorEnabled());
    }

    public function testIsEnabledNotActive(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(false);
        $this->assertFalse($this->config->isEnabled());
    }

    public function testIsDisabledForAdminhtml(): void
    {
        $this->scopeConfigMock->expects($this->exactly(2))->method('isSetFlag')
            ->withConsecutive(
                [Config::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT],
                [Config::CONFIG_ENABLED_ADMINHTML, ScopeConfigInterface::SCOPE_TYPE_DEFAULT]
            )->willReturnOnConsecutiveCalls(true, false);
        $this->appStateMock->expects($this->once())->method('getAreaCode')->willReturn(Area::AREA_ADMINHTML);
        $this->assertFalse($this->config->isEnabled());
    }

    public function testIsAllowedIP(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->scopeConfigMock->expects($this->atLeastOnce())->method('getValue')
            ->with(Config::CONFIG_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn('127.0.0.1, 192.168.1.1');
        $this->assertTrue($this->config->isAllowedIP());
    }

    public function testIsTimeCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_TIME, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isTimeCollectorEnabled());
    }

    public function testGetAllowedIPs(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(Config::CONFIG_ALLOWED_IPS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn('127.0.0.1, 192.168.1.1');
        $this->assertEquals(['127.0.0.1', '192.168.1.1'], $this->config->getAllowedIPs());
    }

    public function testGetTimePrecision(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(Config::CONFIG_TIME_PRECISION, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(2);
        $this->assertEquals(2, $this->config->getTimePrecision());
    }

    public function testGetPerformanceColor(): void
    {
        $configPath = sprintf(Config::CONFIG_PERFORMANCE_COLOR, 'event');
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with($configPath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn('#000');
        $this->assertEquals('#000', $this->config->getPerformanceColor('event'));
    }

    public function testGetCollectorClass(): void
    {
        $this->scopeConfigMock->expects($this->exactly(2))->method('getValue')
            ->with(Config::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(['collector' => 'CollectorClass', 'collector2' => 'CollectorClass2']);
        $this->assertEquals('CollectorClass', $this->config->getCollectorClass('collector'));
    }

    public function testGetCollectorClassNotFound(): void
    {
        $this->expectException(\ClawRock\Debug\Exception\CollectorNotFoundException::class);
        $this->phraseFactoryMock->expects($this->once())->method('create')->willReturn($this->phraseMock);
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(Config::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(['collector' => 'CollectorClass', 'collector2' => 'CollectorClass2']);
        $this->config->getCollectorClass('collector3');
    }

    public function testIsCacheCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_CACHE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isCacheCollectorEnabled());
    }

    public function testIsEventCollectorEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_COLLECTOR_EVENT, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(true);
        $this->assertTrue($this->config->isEventCollectorEnabled());
    }

    public function testIsActive(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Config::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn(false);
        $this->assertFalse($this->config->isActive());
    }

    public function testIsFrontend(): void
    {
        $this->appStateMock->expects($this->once())->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);
        $this->assertTrue($this->config->isFrontend());
    }
}
