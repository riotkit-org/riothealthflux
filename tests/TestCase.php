<?php declare(strict_types=1);

namespace Tests;

use Riotkit\UptimeAdminBoard\Kernel;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private static $backupEnv;
    private static $backupServer;

    public static function setUpBeforeClass()
    {
        self::$backupEnv    = $_ENV;
        self::$backupServer = $_SERVER;

        parent::setUpBeforeClass();
    }

    public function tearDown()
    {
        $_ENV    = self::$backupEnv;
        $_SERVER = self::$backupServer;
    }

    public function createKernel()
    {
        return new Kernel();
    }
}
