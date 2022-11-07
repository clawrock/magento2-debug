<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\CacheAction;
use PHPUnit\Framework\TestCase;

class CacheActionTest extends TestCase
{
    public function testObject(): void
    {
        $id = uniqid();
        $name = CacheAction::LOAD;
        $time = 2.44;
        $info = [
            CacheAction::CACHE_HIT => true,
            CacheAction::CACHE_TAGS => ['tag1', 'tag2'],
            CacheAction::CACHE_TTL => 3600,
        ];
        $cacheAction = new CacheAction($id, $name, $time, $info);

        $this->assertEquals($id, $cacheAction->getId());
        $this->assertEquals($name, $cacheAction->getName());
        $this->assertEquals($time, $cacheAction->getTime());
        $this->assertTrue($cacheAction->isHit());
        $this->assertTrue($cacheAction->isLoad());
        $this->assertEquals($info, $cacheAction->getInfo());
        $this->assertEquals($info[CacheAction::CACHE_TAGS], $cacheAction->getTags());
        $this->assertTrue($cacheAction->hasTags());
        $this->assertEquals($info[CacheAction::CACHE_TTL], $cacheAction->getTTL());
    }
}
