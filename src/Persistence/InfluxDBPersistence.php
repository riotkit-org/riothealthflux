<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Persistence;

use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Point;
use Riotkit\HealthFlux\DTO\Node;

class InfluxDBPersistence implements PersistenceInterface
{
    private Database $client;

    public function __construct(string $url)
    {
        $this->client = Client::fromDSN($url);
    }

    public function persist(Node $node): void
    {
        $this->client->writePoints([
            new Point(
                'riothealthflux',
                $node->isUp(),
                [
                    'checked_by' => $node->getCheckedBy(),
                    'name'       => $node->getName(),
                    'url'        => $node->getUrl(),
                    'ident'      => $node->getName() . '_'. $node->getUrl(),
                    'up'         => $node->isUp()
                ],
                [
                    'up'     => $node->isUp(),
                    'up_int' => (int) $node->isUp(),
                    'id'     => $node->getCheckId(),
                    'url'    => $node->getUrl(),
                    'name'   => $node->getName()
                ]
            )
        ]);
    }
}
