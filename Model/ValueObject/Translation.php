<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class Translation implements LoggableInterface
{
    private string $phrase;
    private string $translation;
    private bool $defined;

    public function __construct(string $phrase, string $translation, bool $defined)
    {
        $this->phrase = $phrase;
        $this->translation = $translation;
        $this->defined = $defined;
    }

    public function getId(): string
    {
        return $this->phrase;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function isDefined(): bool
    {
        return $this->defined;
    }
}
