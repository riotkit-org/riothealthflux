<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Provider;

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
}
