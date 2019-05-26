<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Repository;

use Riotkit\UptimeAdminBoard\Entity\Node;

interface HistoryRepository
{
    public function persist(Node $node): void;
}
