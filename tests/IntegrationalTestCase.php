<?php declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;

class IntegrationalTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        exec('rkd :test-containers:clear :test-containers:setup');

        $this->waitForAddress('http://localhost:8000');
        $this->waitForAddress('http://localhost:8086/health');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        exec('rkd :test-containers:clear > /dev/null');
    }

    protected function waitForAddress(string $address, int $seconds = 10)
    {
        print('!!!! ' . $address);

        $client = new Client(['http_errors' => false]);
        $statusCode = null;

        while ($statusCode >= 300 || $statusCode === null) {
            $statusCode = $client->get($address)->getStatusCode();

            sleep(1);
            $seconds--;

            if ($seconds === 0) {
                throw new \Exception('Still the ' . $address . ' is responding with ' . $statusCode . ', not 2xx');
            }
        }
    }
}
