<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Repository;

use Doctrine\Common\Cache\Cache;

class NodeRepository
{
    private const NODES_GROUPED_IDENT = 'nodes_grouped';

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function findAllGroupedNodes(): array
    {
        if (!$this->cache->contains(self::NODES_GROUPED_IDENT)) {
            return [];
        }

        return $this->cache->fetch(self::NODES_GROUPED_IDENT);
    }

    public function storeGroupedNodes(array $groupedNodes): void
    {
        $this->cache->save(self::NODES_GROUPED_IDENT, $groupedNodes);
    }
}