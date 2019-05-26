<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Service\UptimeRobotApi;
use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * @codeCoverageIgnore
 */
class UptimeRobotProvider implements ServerUptimeProvider
{
    /**
     * @inheritdoc
     */
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array
    {
        $api = $this->createApiInstance(
            $this->unpackUrl($url)['apiKey'],
            $proxyAddress,
            $proxyAuth
        );

        $response = $api->request('/getMonitors');
        $monitors = [];

        if (!$response || empty($response['monitors']['monitor'])) {
            return [];
        }

        foreach ($response['monitors']['monitor'] as $monitor) {
            $monitors[] = new Node(
                $monitor['friendlyname'],
                (int) $monitor['status'] === 2 ? Node::STATUS_UP : Node::STATUS_DOWN,
                $monitor['url'] ?? ''
            );
        }

        return $monitors;
    }

    private function unpackUrl(string $url): array
    {
        $parts = explode('@', substr($url, \strlen('UptimeRobot://')));

        return [
            'apiKey' => $parts[0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function canHandle(string $url): bool
    {
        return strpos($url, 'UptimeRobot://') === 0;
    }

    /**
     * @param string $key
     * @param string $proxyAddress
     * @param string $proxyAuth
     *
     * @return UptimeRobotApi
     * @throws \Exception
     */
    private function createApiInstance(string $key, string $proxyAddress = '', string $proxyAuth = ''): UptimeRobotApi
    {
        return new UptimeRobotApi(
            [
                'url'        => 'https://api.uptimerobot.com',
                'apiKey'     => $key
            ],
            [
                'proxy'      => $proxyAddress,
                'proxy_auth' => $proxyAuth
            ]
        );
    }
}
