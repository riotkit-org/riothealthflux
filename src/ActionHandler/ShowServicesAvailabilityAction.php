<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\ActionHandler;

use Riotkit\UptimeAdminBoard\Component\Config;
use Riotkit\UptimeAdminBoard\Provider\ServerUptimeProvider;
use Riotkit\UptimeAdminBoard\Service\Stats\StatsProcessingService;

class ShowServicesAvailabilityAction
{
    /**
     * @var ServerUptimeProvider $provider
     */
    private $provider;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var StatsProcessingService
     */
    private $stats;

    public function __construct(ServerUptimeProvider $provider, Config $config, StatsProcessingService $stats)
    {
        $this->provider = $provider;
        $this->config   = $config;
        $this->stats    = $stats;
    }
    
    public function handle(): array
    {
        $allNodesGrouped = [];

        foreach ($this->config->get('providers') as $providerUrl) {
            $allNodesGrouped[] = $this->provider->handle(
                $providerUrl,
                $this->config->get('proxy_address', ''),
                $this->config->get('proxy_auth', '')
            );
        }

        return [
            'nodesGrouped'  => $allNodesGrouped,
            'title'         => $this->config->get('title', ''),
            'css'           => $this->config->get('css', ''),
            'canExposeUrls' => $this->config->get('expose_url', true),
            'stats'         => $this->stats->retrieve()
        ];
    }
}
