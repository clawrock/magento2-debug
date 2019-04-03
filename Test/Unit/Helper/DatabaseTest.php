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

    /**
     * @dataProvider queryParametersProvider
     */
    public function testReplaceQueryParameters($query, array $parameters, $result)
    {
        $this->resourceConnectionMock->expects($this->exactly(count($parameters)))->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects($this->exactly(count($parameters)))->method('quote')
            ->willReturnCallback(function () {
                $args = func_get_args();
                return $args[0];
            });

        $this->assertEquals($result, $this->database->replaceQueryParameters($query, $parameters));
    }

    public function queryParametersProvider()
    {
        return [
            ['SELECT * FROM core_config_data WHERE path = ?', [
                'web/secure/base_url'
            ], 'SELECT * FROM core_config_data WHERE path = web/secure/base_url'],
            ['INSERT INTO `table` (`field1`, `field2`) VALUES (\'2000-01-01 10:00:00\', ?)', [
                'value',
            ], 'INSERT INTO `table` (`field1`, `field2`) VALUES (\'2000-01-01 10:00:00\', value)'],
            ['SELECT * FROM core_config_data WHERE path = :path', [
                ':path' => 'web/unsecure/base_url'
            ], 'SELECT * FROM core_config_data WHERE path = web/unsecure/base_url'],
        ];
    }
}
