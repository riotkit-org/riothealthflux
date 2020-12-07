<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Persistence;

use InfluxDB\Client;
use Riotkit\HealthFlux\DTO\Node;
use Riotkit\HealthFlux\Persistence\InfluxDBPersistence;
use Tests\IntegrationalTestCase;

class InfluxDBPersistenceTest extends IntegrationalTestCase
{
    /**
     * Notice: This test spawns docker containers and requires access to docker daemon
     *
     * @group integration
     * @see InfluxDBPersistence::persist()
     */
    public function testPersist(): void
    {
        $dsn       = 'http+influxdb://bakunin:bakunin@127.0.0.1:8086/hulajpole';
        $persister = new InfluxDBPersistence(url: $dsn);

        //
        // Persist a data first
        //
        $uniqueName = 'Kropotkin-' . uniqid();

        $persister->persist(
            new Node(
                name: $uniqueName, checkedBy: 'PHPUnit', status: true,
                url: 'http://127.0.0.1:8000'
            )
        );

        $persister->flush();

        //
        // Let's verify
        //

        $client = Client::fromDSN($dsn);
        $results = $client->query('SELECT * FROM riothealthflux')->getPoints();

        $lastAdded = $results[count($results) - 1];

        $this->assertEquals('PHPUnit', $lastAdded['checked_by']);
        $this->assertEquals($uniqueName . '_http://127.0.0.1:8000', $lastAdded['ident']);
        $this->assertEquals($uniqueName, $lastAdded['name']);
        $this->assertEquals(true, $lastAdded['up']);
        $this->assertEquals('http://127.0.0.1:8000', $lastAdded['url']);
    }
}
