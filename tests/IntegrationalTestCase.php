<?php declare(strict_types=1);

namespace Tests;

class IntegrationalTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        exec('rkd :test-containers:clear :test-containers:setup');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        exec('rkd :test-containers:clear > /dev/null');
    }
}
