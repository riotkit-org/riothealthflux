<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Provider;

use GuzzleHttp\Psr7\Response;
use Riotkit\HealthFlux\Provider\InfracheckProvider;
use Tests\IntegrationalTestCase;

class InfracheckProviderTest extends IntegrationalTestCase
{
    /**
     * Notice: This test spawns docker containers and requires access to docker daemon
     *
     * @group integration
     * @see InfracheckProvider::handle()
     */
    public function testHandle(): void
    {
        $provider = new InfracheckProvider();
        $checks = $provider->handle('Infracheck://http://127.0.0.1:8000');

        $check = $checks[0];

        $this->assertEquals('dir-test', $check->getName());
        $this->assertEquals('http://127.0.0.1:8000', $check->getUrl());
        $this->assertEquals(true, $check->isUp());
    }

    public function testThrowsExceptionWhenResponseIsIncorrectlyFormatted(): void
    {
        $this->expectExceptionMessageMatches('/Invalid response from server. Got 404 code and body/');

        $provider = new InfracheckProvider();
        $provider->handle('Infracheck://http://127.0.0.1:8000/invalid-path');
    }

    public function testHandles5xxErrorsStillReadingTheBody(): void
    {
        //
        // Mock the HTTP call, so the InfracheckProvider will get a FAKE data with 500 error code
        // and a properly formatted response
        //
        $response = new Response(500, [], json_encode([
            'checks' => [
                'anarchizm-info' => [
                    "checked_at"   => "2020-12-07 09-29-38",
                    "hooks_output" => "",
                    "ident"        => "anarchizm-info=True",
                    "output"       => "Domain anarchizm.info is not expired. 213 days left\n",
                    "status"       => true
                ],
                'test-123' => [
                    "checked_at"   => "2020-12-07 09-29-38",
                    "hooks_output" => "",
                    "ident"        => "test-123=False",
                    "output"       => "The check was failed",
                    "status"       => false
                ]
            ],
            'global_status' => false
        ]));

        $mock = $this->getMockBuilder(InfracheckProvider::class);
        $mock->onlyMethods(['fetchResponse']);
        $provider = $mock->getMock();

        $provider->method('fetchResponse')->willReturn($response);

        //
        // Run and assert that there are two checks parsed from the response, even if 500 error happened
        //
        $this->assertCount(2, $provider->handle('Infracheck://http://in-memory:8000'));
    }
}
