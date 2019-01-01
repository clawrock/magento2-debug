<?php

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\Translation;
use Magento\Framework\Phrase\Renderer\Translate;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TranslationCollectorPlugin
{
    /**
     * @var \Magento\Framework\TranslateInterface
     */
    private $translate;

    /**
     * @var array
     */
    private $translations;

    /**
     * @var \ClawRock\Debug\Model\Collector\TranslationCollector
     */
    private $translationCollector;

    public function __construct(
        \Magento\Framework\TranslateInterface $translate,
        \ClawRock\Debug\Model\Collector\TranslationCollector $translationCollector
    ) {
        $this->translate = $translate;
        $this->translationCollector = $translationCollector;
    }

    public function beforeRender(Translate $subject, array $source, array $arguments)
    {
        $text = end($source);
        $text = str_replace('\"', '"', $text);
        $text = str_replace("\\'", "'", $text);

        $data = $this->getTranslations();
        $translation = '';

        if ($isDefined = array_key_exists($text, $data)) {
            $translation = $data[$text];
        }

        $this->translationCollector->log(new Translation(end($source), $translation, $isDefined));

        return null;
    }

    private function getTranslations(): array
    {
        if ($this->translations === null) {
            $this->translations = $this->translate->getData();
        }

        return $this->translations;
    }
}
