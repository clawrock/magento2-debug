<?php

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\TranslationCollectorPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class TranslationCollectorPluginTest extends TestCase
{
    public function testBeforeRender()
    {
        $subjectMock = $this->getMockBuilder(\Magento\Framework\Phrase\Renderer\Translate::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translateMock = $this->getMockForAbstractClass(\Magento\Framework\TranslateInterface::class);

        $translationCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\TranslationCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $plugin = (new ObjectManager($this))->getObject(TranslationCollectorPlugin::class, [
            'translate' => $translateMock,
            'translationCollector' => $translationCollectorMock,
        ]);

        $translateMock->expects($this->once())->method('getData')->willReturn(['text' => 'translation']);

        $source = ['text'];
        $args = [];

        $this->assertNull($plugin->beforeRender($subjectMock, $source, $args));
    }
}
