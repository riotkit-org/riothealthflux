<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Persistence;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use Riotkit\UptimeAdminBoard\Entity\Node;

class InfluxDBPersistence implements PersistenceInterface
{
    private Client $client;

    public function __construct(string $url, string $token, string $bucket, string $org)
    {
        $this->client = new Client([
            'url'    => $url,
            'token'  => $token,
            'bucket' => $bucket,
            'org'    => $org,
            'precision' => WritePrecision::NS
        ]);
    }

    public function persist(Node $node): void
    {
        $writeClient = $this->client->createWriteApi();
        $writeClient->write([
           [
               'name' => 'riothealthflux',
               'tags' => [
                   'checked_by' => $node->getCheckedBy(),
                   'name'       => $node->getName(),
                   'url'        => $node->getUrl()
               ],
               'fields' => [
                   'up' => $node->isUp(),
                   'id' => $node->getCheckId()
               ],
               'time' => time()
           ]
        ]);
        $writeClient->close();
    }
}
