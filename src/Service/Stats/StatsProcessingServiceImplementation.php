<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service\Stats;

use Riotkit\UptimeAdminBoard\Collection\HistoriesCollection;
use Riotkit\UptimeAdminBoard\Repository\HistoryRepository;

class StatsProcessingServiceImplementation implements StatsProcessingService
{
    /**
     * @var HistoryRepository
     */
    private $repository;

    /**
     * @var array[]
     */
    private $processed = [];

    /**
     * @var HistoriesCollection
     */
    private $all;

    public function __construct(HistoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function retrieve(): array
    {
        $this->warmUpIfNecessary();

        return $this->processed;
    }

    private function warmUpIfNecessary(): void
    {
        if ($this->processed) {
            return;
        }

        $this->all = $this->repository->findAllGrouped();

        $this->processed = [
            'mostUnstableInCurrent24Hours'  => $this->all->findMostUnstableInLast24Hours(10),
            'topFailing'                    => $this->all->findMostlyFailingNodes(10),
            'recentlyFixed'                 => $this->all->findRecentlyFixed(10),
            'failingChecks'                 => $this->repository->findFailingCount(),
            'succeedingChecks'              => $this->repository->findSuccessCount()
        ];
    }
}
