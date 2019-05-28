<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service\Stats;

class DisabledStatsProcessingService implements StatsProcessingService
{
    public function retrieve(): array
    {
        return [
            'mostUnstableInCurrent24Hours'  => [],
            'topFailing'                    => [],
            'recentlyFixed'                 => [],
            'failingChecks'                 => 0,
            'succeedingChecks'              => 0
        ];
    }
}
