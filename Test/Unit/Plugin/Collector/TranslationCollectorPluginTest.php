<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\TranslationCollectorPlugin;
use PHPUnit\Framework\TestCase;

class TranslationCollectorPluginTest extends TestCase
{
    public function testBeforeRender(): void
    {
        $subjectMock = $this->getMockBuilder(\Magento\Framework\Phrase\Renderer\Translate::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translateMock = $this->getMockForAbstractClass(\Magento\Framework\TranslateInterface::class);

        $translationCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\TranslationCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $plugin = new TranslationCollectorPlugin($translateMock, $translationCollectorMock);

        $translateMock->expects($this->once())->method('getData')->willReturn(['text' => 'translation']);

        $source = ['text'];
        $args = [];

        $plugin->beforeRender($subjectMock, $source, $args);
    }
}
