<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;

class TranslationCollector implements CollectorInterface, LoggerCollectorInterface
{
    public const NAME = 'translation';
    public const TRANSLATIONS = 'translations';
    public const DEFINED = 'defined';
    public const MISSING = 'missing';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Logger\DataLogger $dataLogger;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Logger\DataLoggerFactory $dataLoggerFactory
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLoggerFactory->create();
    }

    public function collect(): CollectorInterface
    {
        $defined = [];
        $missing = [];
        /** @var \ClawRock\Debug\Model\ValueObject\Translation $translation */
        foreach ($this->dataLogger->getLogs() as $translation) {
            if ($translation->isDefined()) {
                $defined[$translation->getId()] = $translation->getTranslation();
                continue;
            }
            $missing[$translation->getId()] = $translation->getPhrase();
        }

        $this->dataCollector->setData([
            self::TRANSLATIONS => [
                self::DEFINED => $defined,
                self::MISSING => $missing,
            ],
        ]);

        return $this;
    }

    public function getTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS) ?? [];
    }

    public function getDefinedTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS)[self::DEFINED] ?? [];
    }

    public function getMissingTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS)[self::MISSING] ?? [];
    }

    public function getTotal(): int
    {
        return count($this->getDefinedTranslations()) + count($this->getMissingTranslations());
    }

    public function isEnabled(): bool
    {
        return $this->config->isTranslationCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        if (!empty($this->getMissingTranslations())) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
