<?php

namespace Doctrine\DBAL\Tests\Functional\Driver\Mysqli;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Mysqli\Connection;
use Doctrine\DBAL\Driver\Mysqli\Driver;
use Doctrine\DBAL\Tests\FunctionalTestCase;
use Doctrine\DBAL\Tests\TestUtil;

use function array_merge;
use function extension_loaded;

use const MYSQLI_OPT_CONNECT_TIMEOUT;

class ConnectionTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('mysqli')) {
            self::markTestSkipped('mysqli is not installed.');
        }

        parent::setUp();

        if ($this->connection->getDriver() instanceof Driver) {
            return;
        }

        self::markTestSkipped('MySQLi only test.');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testSupportedDriverOptions(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getConnection([MYSQLI_OPT_CONNECT_TIMEOUT => 1]);
    }

    public function testUnsupportedDriverOption(): void
    {
        $this->expectException(Exception::class);

        $this->getConnection([12345 => 'world']);
    }

    public function testInvalidCharset(): void
    {
        $params = TestUtil::getConnectionParams();

        $this->expectException(Exception::class);
        (new Driver())->connect(
            array_merge(
                $params,
                ['charset' => 'invalid']
            )
        );
    }

    /**
     * @param mixed[] $driverOptions
     */
    private function getConnection(array $driverOptions): Connection
    {
        $params = TestUtil::getConnectionParams();

        return (new Driver())->connect(
            array_merge(
                $params,
                ['driver_options' => $driverOptions]
            )
        );
    }
}