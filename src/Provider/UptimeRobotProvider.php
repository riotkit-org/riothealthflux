<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Service\UptimeRobotApi;
use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * @codeCoverageIgnore
 */
class UptimeRobotProvider implements ServerUptimeProvider
{
    private const CHECK_TYPE = 'UptimeRobot';

    /**
     * @inheritdoc
     */
    public function handle(string $url): array
    {
        $api = $this->createApiInstance(
            $this->unpackUrl($url)['apiKey'],
        );

        $response = $api->request('/getMonitors');
        $monitors = [];

        if (!$response || empty($response['monitors']['monitor'])) {
            return [];
        }

        foreach ($response['monitors']['monitor'] as $monitor) {
            $monitors[] = new Node(
                $monitor['friendlyname'],
                self::CHECK_TYPE,
                (int) $monitor['status'] === 2 ? Node::STATUS_UP : Node::STATUS_DOWN,
                $monitor['url'] ?? '',
                null
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
     *
     * @return UptimeRobotApi
     * @throws \Exception
     */
    private function createApiInstance(string $key): UptimeRobotApi
    {
        return new UptimeRobotApi(
            [
                'url'        => 'https://api.uptimerobot.com',
                'apiKey'     => $key
            ]
        );
    }
}
