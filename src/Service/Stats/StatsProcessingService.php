<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service\Stats;

interface StatsProcessingService
{
    public function retrieve(): array;
}
