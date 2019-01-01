<?php

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class Translation implements LoggableInterface
{
    /**
     * @var string
     */
    private $phrase;

    /**
     * @var string
     */
    private $translation;

    /**
     * @var bool
     */
    private $defined;

    public function __construct(string $phrase, string $translation, bool $defined)
    {
        $this->phrase = $phrase;
        $this->translation = $translation;
        $this->defined = $defined;
    }

    public function getId()
    {
        return $this->phrase;
    }

    /**
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * @return string
     */
    public function getTranslation(): string
    {
        return $this->translation;
    }

    /**
     * @return bool
     */
    public function isDefined(): bool
    {
        return $this->defined;
    }
}
