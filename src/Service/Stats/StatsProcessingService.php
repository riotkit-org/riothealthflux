<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service\Stats;

use Riotkit\UptimeAdminBoard\Entity\Node;
use Riotkit\UptimeAdminBoard\Repository\HistoryRepository;
use Riotkit\UptimeAdminBoard\Repository\StatsRepository;

class StatsProcessingService
{
    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var StatsRepository
     */
    private $statsRepository;

    public function __construct(HistoryRepository $historyRepository, StatsRepository $statsRepository)
    {
        $this->historyRepository = $historyRepository;
        $this->statsRepository   = $statsRepository;
    }

    public function warmUp(array $groupedCurrentNodes): void
    {
        $all = $this->historyRepository->findAllGrouped();
        $countByStatus = $this->historyRepository->findCurrentCountByStatus();

        $stats = [
            'mostUnstableInCurrent24Hours'  => $all->findMostUnstableInLast24Hours(15),
            'topFailing'                    => $all->findMostlyFailingNodes(15),
            'recentlyFixed'                 => $all->findRecentlyFixed(15),
            'failingChecks'                 => $countByStatus['failing'],
            'succeedingChecks'              => $countByStatus['success'],
            'countByHour'                   => $all->findCountPerHour(),
            'nodesOrderedByStatus'          => $this->sortByStatus($groupedCurrentNodes)
        ];

        $this->statsRepository->storeStats($stats);
    }

    /**
     * @param array[] $nodesGrouped
     *
     * @return Node[]
     */
    private function sortByStatus(array $nodesGrouped): array
    {
        $nodes = $nodesGrouped ? \array_merge(...$nodesGrouped) : [];

        usort($nodes, static function(Node $a, Node $b) {
            return $a->getStatus() <=> $b->getStatus();
        });

        return $nodes;
    }
}
