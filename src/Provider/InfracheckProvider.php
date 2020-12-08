<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Provider;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Riotkit\HealthFlux\DTO\Node;

class InfracheckProvider implements ServerUptimeProviderInterface
{
    protected const PREFIX        = 'Infracheck://';
    protected const PROVIDER_NAME = 'Infracheck';

    /**
     * @inheritDoc
     */
    public function canHandle(string $url): bool
    {
        return str_starts_with($url, static::PREFIX);
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function handle(string $url): array
    {
        $url = substr($url, strlen(static::PREFIX));

        $response = $this->fetchResponse($url);
        $asArray = json_decode($response->getBody()->getContents(), true);

        if (!isset($asArray['checks'])) {
            throw new \Exception(
                'Invalid response from server. Got ' . $response->getStatusCode() . ' ' .
                'code and body: ' . $response->getBody()->getContents()
            );
        }

        $result = [];

        foreach ($asArray['checks'] as $name => $details) {
            $result[] = new Node(
                name: $name,
                checkedBy: static::PROVIDER_NAME,
                status: $details['status'],
                url: $url,
                description: $details['output']
            );
        }

        return $result;
    }

    protected function fetchResponse(string $url): ResponseInterface
    {
        $http = new Client(['http_errors' => false]);
        return $http->get($url);
    }
}
