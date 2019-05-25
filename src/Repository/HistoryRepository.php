<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Repository;

use Wolnosciowiec\UptimeAdminBoard\Entity\Node;

interface HistoryRepository
{
    public function persist(Node $node): void;
}
