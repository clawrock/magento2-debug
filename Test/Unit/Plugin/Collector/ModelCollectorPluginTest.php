<?php

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\ModelCollectorPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ModelCollectorPluginTest extends TestCase
{
    private $modelCollectorMock;

    private $formatterMock;

    private $debugMock;

    private $objectMock;

    private $proceedMock;

    private $subjectMock;

    private $plugin;

    protected function setUp()
    {
        $this->modelCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\ModelCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formatterMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Formatter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->debugMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Debug::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectMock = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->proceedMock = function () {
            return true;
        };

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\Model\ResourceModel\AbstractResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = (new ObjectManager($this))->getObject(ModelCollectorPlugin::class, [
            'modelCollector' => $this->modelCollectorMock,
            'formatter' => $this->formatterMock,
            'debug' => $this->debugMock,
        ]);
    }

    public function testAroundSave()
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn(1.00);
        $this->assertTrue($this->plugin->aroundSave($this->subjectMock, $this->proceedMock, $this->objectMock));
    }

    public function testAroundDelete()
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn(1.00);
        $this->assertTrue($this->plugin->aroundDelete($this->subjectMock, $this->proceedMock, $this->objectMock));
    }

    public function testAroundLoad()
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn(1.00);
        $this->assertTrue($this->plugin->aroundLoad($this->subjectMock, $this->proceedMock, $this->objectMock, 1));
    }
}
