<?php

namespace ClawRock\Debug\Plugin\Collector;

class TranslationDataCollectorPlugin
{
    /**
     * @var \Magento\Framework\TranslateInterface
     */
    private $translate;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \ClawRock\Debug\Model\DataCollector\TranslationDataCollector
     */
    private $dataCollector;

    public function __construct(
        \Magento\Framework\TranslateInterface $translate,
        \Psr\Log\LoggerInterface $logger,
        \ClawRock\Debug\Model\DataCollector\TranslationDataCollector $dataCollector
    ) {
        $this->translate = $translate;
        $this->logger = $logger;
        $this->dataCollector = $dataCollector;
    }

    public function aroundRender(\Magento\Framework\Phrase\Renderer\Translate $subject, callable $proceed, array $source, array $arguments)
    {
        $text = end($source);
        $text = str_replace('\"', '"', $text);
        $text = str_replace("\\'", "'", $text);

        try {
            $data = $this->translate->getData();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }

        $translationExists = array_key_exists($text, $data);

        $translationExists ? $this->dataCollector->collectDefinedPhrase(end($source), $data[$text]) : $this->dataCollector->collectMissingPhrase(end($source));

        return $translationExists ? $data[$text] : end($source);
    }
}
