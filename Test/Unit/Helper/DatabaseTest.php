<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Helper;

use ClawRock\Debug\Helper\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /** @var \Magento\Framework\App\ResourceConnection&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\ResourceConnection $resourceConnectionMock;
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\DB\Adapter\AdapterInterface $connectionMock;
    private \ClawRock\Debug\Helper\Database $database;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resourceConnectionMock = $this->getMockBuilder(\Magento\Framework\App\ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockForAbstractClass(\Magento\Framework\DB\Adapter\AdapterInterface::class);

        $this->database = new Database($this->resourceConnectionMock);
    }

    public function testFormatQuery(): void
    {
        $query = 'SELECT * FROM core_config_data;';
        $formattedQuery = \SqlFormatter::format($query);

        $this->assertEquals($formattedQuery, $this->database->formatQuery($query));
        $this->assertEquals($formattedQuery, $this->database->formatQuery($query)); // Cache test
    }

    public function testHighlightQuery(): void
    {
        $query = 'SELECT * FROM core_config_data;';
        $highlightedQuery = \SqlFormatter::highlight($query);

        $this->assertEquals($highlightedQuery, $this->database->highlightQuery($query));
        $this->assertEquals($highlightedQuery, $this->database->highlightQuery($query)); // Cache test
    }

    public function testGetQueryId(): void
    {
        $query = 'query';
        $params = ['param1', 'param2'];

        $profilerQueryMock = $this->getMockBuilder(\Zend_Db_Profiler_Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $profilerQueryMock->expects($this->once())->method('getQuery')->willReturn($query);
        $profilerQueryMock->expects($this->once())->method('getQueryParams')->willReturn($params);

        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        $this->assertEquals(md5($query . implode(',', $params)), $this->database->getQueryId($profilerQueryMock));
    }

    /**
     * @dataProvider queryParametersProvider
     *
     * @param string $query
     * @param array  $parameters
     * @param string $result
     */
    public function testReplaceQueryParameters($query, array $parameters, $result): void
    {
        $quoteValueCount = count($parameters) ?: 1;
        $this->resourceConnectionMock->expects($this->exactly($quoteValueCount))->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects($this->exactly($quoteValueCount))->method('quote')
            ->willReturnCallback(function () {
                $args = func_get_args();
                return $args[0];
            });

        $this->assertEquals($result, $this->database->replaceQueryParameters($query, $parameters));
    }

    public function queryParametersProvider(): array
    {
        return [
            ['SELECT * FROM core_config_data WHERE path = ?', [
                'web/secure/base_url',
            ], 'SELECT * FROM core_config_data WHERE path = web/secure/base_url'],
            ['INSERT INTO `table` (`field1`, `field2`) VALUES (\'2000-01-01 10:00:00\', ?)', [
                'value',
            ], 'INSERT INTO `table` (`field1`, `field2`) VALUES (\'2000-01-01 10:00:00\', value)'],
            ['SELECT * FROM core_config_data WHERE path = :path', [
                ':path' => 'web/unsecure/base_url',
            ], 'SELECT * FROM core_config_data WHERE path = web/unsecure/base_url'],
            [
                'SELECT COUNT(*) FROM table WHERE (url = \'https://example.com/?utm_source=backend\')',
                [],
                'SELECT COUNT(*) FROM table WHERE (url = \'https://example.com/?utm_source=backend\')',
            ],
        ];
    }
}
