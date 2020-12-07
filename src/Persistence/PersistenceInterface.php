<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Persistence;

use Riotkit\HealthFlux\DTO\Node;

interface PersistenceInterface
{
    public function persist(Node $node): void;
    public function flush(): void;
}
