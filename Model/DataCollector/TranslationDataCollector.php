<?php

namespace ClawRock\Debug\Model\DataCollector;

class TranslationDataCollector extends AbstractDataCollector
{
    const NAME = 'translation';

    const DEFINED = 'defined';
    const MISSING = 'missing';

    const PHRASES      = 'phrases';
    const STATE_COUNTS = 'state_counts';

    /**
     * @var array
     */
    protected $data = [
        self::STATE_COUNTS => [
            self::DEFINED => 0,
            self::MISSING => 0,
        ],
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        // Data is collected with TranslationDataCollectorPlugin
        return $this;
    }

    public function collectDefinedPhrase(string $phrase, $translation)
    {
        $this->data[self::PHRASES][self::DEFINED][$phrase] = $translation;
        $this->data[self::STATE_COUNTS][self::DEFINED]++;
    }

    public function collectMissingPhrase(string $phrase)
    {
        $this->data[self::PHRASES][self::MISSING][] = $phrase;
        $this->data[self::STATE_COUNTS][self::MISSING]++;
    }

    public function getPhrases()
    {
        return $this->data[self::PHRASES] ?? [];
    }

    public function getTranslatedPhrases()
    {
        return $this->data[self::PHRASES][self::DEFINED] ?? [];
    }

    public function getMissingPhrases()
    {
        return $this->data[self::PHRASES][self::MISSING] ?? [];
    }

    public function countPhrases()
    {
        $count = 0;
        foreach ($this->data[self::STATE_COUNTS] as $stateCount) {
            $count += $stateCount;
        }

        return $count;
    }

    public function countState($status)
    {
        return $this->data[self::STATE_COUNTS][$status] ?? 0;
    }

    public function countDefined()
    {
        return $this->countState(self::DEFINED);
    }

    public function countMissing()
    {
        return $this->countState(self::MISSING);
    }

    public function countUnique($status)
    {
        if (!isset($this->data[self::PHRASES][$status])) {
            return 0;
        }

        return count($this->data[self::PHRASES][$status]);
    }

    public function countUniqueDefined()
    {
        return $this->countUnique(self::DEFINED);
    }

    public function countUniqueMissing()
    {
        return $this->countUnique(self::MISSING);
    }

    public function isEnabled()
    {
        return $this->helper->isTranslationDataCollectorEnabled();
    }
}
