<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service\Health;

use Riotkit\UptimeAdminBoard\Component\Config;
use Riotkit\UptimeAdminBoard\Provider\ServerUptimeProvider;
use Riotkit\UptimeAdminBoard\Repository\NodeRepository;

class StatusProcessingService
{
    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var ServerUptimeProvider
     */
    private $provider;

    /**
     * @var Config
     */
    private $config;

    public function __construct(ServerUptimeProvider $provider, Config $config, NodeRepository $nodeRepository)
    {
        $this->provider       = $provider;
        $this->config         = $config;
        $this->nodeRepository = $nodeRepository;
    }

    public function warmUp(): array
    {
        $allNodesGrouped = [];

        foreach ($this->config->get('providers') as $providerUrl) {
            $allNodesGrouped[] = $this->provider->handle(
                $providerUrl,
                $this->config->get('proxy_address', ''),
                $this->config->get('proxy_auth', '')
            );
        }

        $this->nodeRepository->storeGroupedNodes($allNodesGrouped);

        return $allNodesGrouped;
    }
}
