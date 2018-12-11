<?php

namespace ClawRock\Debug\Test\Unit\Model\Storage;

use ClawRock\Debug\Model\Storage\HttpStorage;
use PHPUnit\Framework\TestCase;

class HttpStorageTest extends TestCase
{
    public function testStorage()
    {
        $requestMock = $this->getMockBuilder(\Magento\Framework\HTTP\PhpEnvironment\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(\Magento\Framework\HTTP\PhpEnvironment\Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage = new HttpStorage();
        $storage->setRequest($requestMock);
        $storage->setResponse($responseMock);
        $this->assertFalse($storage->isFPCRequest());
        $this->assertTrue($storage->markAsFPCRequest()->isFPCRequest());
        $this->assertEquals($requestMock, $storage->getRequest());
        $this->assertEquals($responseMock, $storage->getResponse());
    }
}
