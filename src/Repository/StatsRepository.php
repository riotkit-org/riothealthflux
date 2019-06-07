<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Repository;

use Doctrine\Common\Cache\Cache;

class StatsRepository
{
    private const STATS_IDENT = 'stats';

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function findStats(): array
    {
        if (!$this->cache->contains(self::STATS_IDENT)) {
            return [];
        }

        return $this->cache->fetch(self::STATS_IDENT);
    }

    public function storeStats(array $stats): void
    {
        $this->cache->save(self::STATS_IDENT, $stats);
    }
}