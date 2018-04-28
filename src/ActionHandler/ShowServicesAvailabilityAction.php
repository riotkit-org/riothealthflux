<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\ActionHandler;

use Wolnosciowiec\UptimeAdminBoard\Component\Config;
use Wolnosciowiec\UptimeAdminBoard\Provider\ServerUptimeProvider;

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

    public function __construct(ServerUptimeProvider $provider, Config $config)
    {
        $this->provider     = $provider;
        $this->config       = $config;
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
            'nodes'         => $allNodesGrouped ? array_merge(...$allNodesGrouped) : [],
            'title'         => $this->config->get('title', ''),
            'css'           => $this->config->get('css', ''),
            'canExposeUrls' => $this->config->get('expose_url', true)
        ];
    }
}
