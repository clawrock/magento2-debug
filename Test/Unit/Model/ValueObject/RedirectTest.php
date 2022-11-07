<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    public function testObject(): void
    {
        $token = 'token';
        $action = 'action';
        $method = 'method';
        $statusCode = 200;
        $statusText = 'status_text';

        $redirect = new Redirect($token, $action, $method, $statusCode, $statusText);

        $this->assertEquals($token, $redirect->getToken());
        $this->assertEquals($action, $redirect->getAction());
        $this->assertEquals($method, $redirect->getMethod());
        $this->assertFalse($redirect->isEmpty());
        $this->assertEquals([
            Redirect::TOKEN       => $token,
            Redirect::ACTION      => $action,
            Redirect::METHOD      => $method,
            Redirect::STATUS_CODE => $statusCode,
            Redirect::STATUS_TEXT => $statusText,
        ], $redirect->toArray());

        $redirect = Redirect::createFromArray(null);
        $this->assertEquals('', $redirect->getToken());
        $this->assertEquals('', $redirect->getAction());
        $this->assertEquals('', $redirect->getMethod());
        $this->assertTrue($redirect->isEmpty());

        $redirect = Redirect::createFromArray([
            Redirect::TOKEN       => $token,
            Redirect::ACTION      => $action,
            Redirect::METHOD      => $method,
            Redirect::STATUS_CODE => $statusCode,
            Redirect::STATUS_TEXT => $statusText,
        ]);

        $this->assertEquals($token, $redirect->getToken());
        $this->assertEquals($action, $redirect->getAction());
        $this->assertEquals($method, $redirect->getMethod());
        $this->assertFalse($redirect->isEmpty());
        $this->assertEquals([
            Redirect::TOKEN       => $token,
            Redirect::ACTION      => $action,
            Redirect::METHOD      => $method,
            Redirect::STATUS_CODE => $statusCode,
            Redirect::STATUS_TEXT => $statusText,
        ], $redirect->toArray());
    }
}
