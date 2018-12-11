<?php

namespace ClawRock\Debug\Test\Unit\Helper;

use ClawRock\Debug\Helper\Database;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $resourceConnectionMock;

    private $connectionMock;

    private $database;

    protected function setUp()
    {
        parent::setUp();

        $this->resourceConnectionMock = $this->getMockBuilder(\Magento\Framework\App\ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockForAbstractClass(\Magento\Framework\DB\Adapter\AdapterInterface::class);

        $this->database = (new ObjectManager($this))->getObject(Database::class, [
            'resourceConnection' => $this->resourceConnectionMock
        ]);
    }

    public function testFormatQuery()
    {
        $query = 'SELECT * FROM core_config_data;';
        $formattedQuery = \SqlFormatter::format($query);

        $this->assertEquals($formattedQuery, $this->database->formatQuery($query));
        $this->assertEquals($formattedQuery, $this->database->formatQuery($query)); // Cache test
    }

    public function testHighlightQuery()
    {
        $query = 'SELECT * FROM core_config_data;';
        $highlightedQuery = \SqlFormatter::highlight($query);

        $this->assertEquals($highlightedQuery, $this->database->highlightQuery($query));
        $this->assertEquals($highlightedQuery, $this->database->highlightQuery($query)); // Cache test
    }

    public function testGetQueryId()
    {
        $query = 'query';
        $params = ['param1', 'param2'];

        $profilerQueryMock = $this->getMockBuilder(\Zend_Db_Profiler_Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $profilerQueryMock->expects($this->once())->method('getQuery')->willReturn($query);
        $profilerQueryMock->expects(($this->once()))->method('getQueryParams')->willReturn($params);

        $this->assertEquals(md5($query . implode(',', $params)), $this->database->getQueryId($profilerQueryMock));
    }

    public function testReplaceQueryParameters()
    {
        $query = 'SELECT * FROM core_config_data WHERE path = :path';
        $parameters = [':path' => 'web/secure/base_url'];
        $result = 'SELECT * FROM core_config_data WHERE path = web/secure/base_url';
        $this->resourceConnectionMock->expects($this->once())->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->connectionMock->expects($this->once())->method('quote')
            ->with($parameters[':path'])
            ->willReturn($parameters[':path']);

        $this->assertEquals($result, $this->database->replaceQueryParameters($query, $parameters));
    }

    public function testReplaceQueryParameters2()
    {
        $query = 'SELECT * FROM core_config_data WHERE path = ?';
        $parameters = ['web/secure/base_url'];
        $result = 'SELECT * FROM core_config_data WHERE path = web/secure/base_url';
        $this->resourceConnectionMock->expects($this->once())->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->connectionMock->expects($this->once())->method('quote')
            ->with($parameters[0])
            ->willReturn($parameters[0]);

        $this->assertEquals($result, $this->database->replaceQueryParameters($query, $parameters));
    }
}
