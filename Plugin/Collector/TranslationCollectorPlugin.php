<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\Translation;
use Magento\Framework\Phrase\Renderer\Translate;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TranslationCollectorPlugin
{
    private \Magento\Framework\TranslateInterface $translate;
    private ?array $translations = null;
    private \ClawRock\Debug\Model\Collector\TranslationCollector $translationCollector;

    public function __construct(
        \Magento\Framework\TranslateInterface $translate,
        \ClawRock\Debug\Model\Collector\TranslationCollector $translationCollector
    ) {
        $this->translate = $translate;
        $this->translationCollector = $translationCollector;
    }

    public function beforeRender(Translate $subject, array $source, array $arguments): void
    {
        /** @var string $text */
        $text = end($source);
        $text = str_replace('\"', '"', $text);
        $text = str_replace("\\'", "'", $text);

        $data = $this->getTranslations();
        $translation = '';

        if ($isDefined = array_key_exists($text, $data)) {
            $translation = $data[$text];
        }

        $this->translationCollector->log(new Translation(end($source), $translation, $isDefined));
    }

    private function getTranslations(): array
    {
        if ($this->translations === null) {
            $this->translations = $this->translate->getData();
        }

        return $this->translations;
    }
}
