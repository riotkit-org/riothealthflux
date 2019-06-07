<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\ActionHandler;

use Riotkit\UptimeAdminBoard\Component\Config;
use Riotkit\UptimeAdminBoard\Repository\NodeRepository;
use Riotkit\UptimeAdminBoard\Repository\StatsRepository;

class ShowServicesAvailabilityAction
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StatsRepository
     */
    private $statsRepository;

    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    public function __construct(Config $config, NodeRepository $nodeRepository, StatsRepository $statsRepository)
    {
        $this->config          = $config;
        $this->statsRepository = $statsRepository;
        $this->nodeRepository  = $nodeRepository;
    }

    public function handle(): array
    {
        $allNodesGrouped = $this->nodeRepository->findAllGroupedNodes();
        $stats = $this->statsRepository->findStats();

        return [
            'nodesGrouped'  => $allNodesGrouped,
            'title'         => $this->config->get('title', ''),
            'css'           => $this->config->get('css', ''),
            'canExposeUrls' => $this->config->get('expose_url', true),
            'stats'         => $stats
        ];
    }
}
