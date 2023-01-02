<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\SearchResult;
use PHPUnit\Framework\TestCase;

class SearchResultTest extends TestCase
{
    public function testObject(): void
    {
        $token = 'token';
        $ip = 'ip';
        $method = 'method';
        $url = 'url';
        $time = time();
        $statusCode = '200';
        $fileSize = '1234';
        $parentToken = 'parent_token';
        $requestTime = '25';

        $searchResult = new SearchResult(
            $token,
            $ip,
            $method,
            $url,
            $time,
            $statusCode,
            $fileSize,
            $parentToken,
            $requestTime
        );

        $this->assertEquals($token, $searchResult->getToken());
        $this->assertEquals($ip, $searchResult->getIp());
        $this->assertEquals($method, $searchResult->getMethod());
        $this->assertEquals($url, $searchResult->getUrl());
        $this->assertEquals($time, $searchResult->getTime());
        $this->assertEquals($statusCode, $searchResult->getStatusCode());
        $this->assertEquals($fileSize, $searchResult->getFileSize());
        $this->assertEquals($parentToken, $searchResult->getParentToken());
        $this->assertEquals($requestTime, $searchResult->getRequestTime());
        $this->assertEquals($time, $searchResult->getDatetime()->getTimestamp());
        $this->assertEquals(SearchResult::STATUS_SUCCESS, $searchResult->getStatus());

        $searchResultFromCsv = SearchResult::createFromCsv([
            $token,
            $ip,
            $method,
            $url,
            $time,
            $statusCode,
            $fileSize,
            $parentToken,
            $requestTime,
        ]);

        $this->assertEquals($searchResult, $searchResultFromCsv);

        $searchResult = new SearchResult($token, $ip, $method, $url, $time, '302', $fileSize, $parentToken);
        $this->assertEquals(SearchResult::STATUS_WARNING, $searchResult->getStatus());

        $searchResult = new SearchResult($token, $ip, $method, $url, $time, '404', $fileSize, $parentToken);
        $this->assertEquals(SearchResult::STATUS_ERROR, $searchResult->getStatus());
    }
}
