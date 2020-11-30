<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Persistence;

use Riotkit\UptimeAdminBoard\Entity\Node;

interface PersistenceInterface
{
    public function persist(Node $node): void;
}
