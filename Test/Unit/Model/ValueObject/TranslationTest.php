<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testObject(): void
    {
        $phrase = 'phrase';
        $translation = 'translation';
        $defined = true;

        $object = new Translation($phrase, $translation, $defined);

        $this->assertEquals($phrase, $object->getPhrase());
        $this->assertEquals($phrase, $object->getId());
        $this->assertEquals($translation, $object->getTranslation());
        $this->assertTrue($object->isDefined());
    }
}
