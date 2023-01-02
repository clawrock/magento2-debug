<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\ModelCollectorPlugin;
use PHPUnit\Framework\TestCase;

class ModelCollectorPluginTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Collector\ModelCollector&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\ModelCollector $modelCollectorMock;
    /** @var \ClawRock\Debug\Helper\Formatter&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Formatter $formatterMock;
    /** @var \ClawRock\Debug\Helper\Debug&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Debug $debugMock;
    /** @var \Magento\Framework\Model\AbstractModel&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Model\AbstractModel $objectMock;
    private \Closure $proceedMock;
    /** @var \Magento\Framework\Model\ResourceModel\AbstractResource&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Model\ResourceModel\AbstractResource $subjectMock;
    private \ClawRock\Debug\Plugin\Collector\ModelCollectorPlugin $plugin;

    protected function setUp(): void
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

        $this->objectMock = $this->getMockBuilder(\Magento\Framework\Model\AbstractModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->proceedMock = function () {
            return true;
        };

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\Model\ResourceModel\AbstractResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new ModelCollectorPlugin($this->modelCollectorMock, $this->formatterMock, $this->debugMock);
    }

    public function testAroundSave(): void
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn('1.00');
        $this->assertTrue($this->plugin->aroundSave($this->subjectMock, $this->proceedMock, $this->objectMock));
    }

    public function testAroundDelete(): void
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn('1.00');
        $this->assertTrue($this->plugin->aroundDelete($this->subjectMock, $this->proceedMock, $this->objectMock));
    }

    public function testAroundLoad(): void
    {
        $this->modelCollectorMock->expects($this->once())->method('log');
        $this->formatterMock->expects($this->once())->method('microtime')->willReturn('1.00');
        $this->assertTrue($this->plugin->aroundLoad($this->subjectMock, $this->proceedMock, $this->objectMock, 1));
    }
}
