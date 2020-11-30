<?php declare(strict_types=1);

namespace Tests;

use Riotkit\HealthFlux\Kernel;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private static array $backupEnv;
    private static array $backupServer;

    public static function setUpBeforeClass(): void
    {
        self::$backupEnv    = $_ENV;
        self::$backupServer = $_SERVER;

        parent::setUpBeforeClass();
    }

    public function tearDown(): void
    {
        $_ENV    = self::$backupEnv;
        $_SERVER = self::$backupServer;
    }

    public function createKernel()
    {
        return new Kernel();
    }
}
