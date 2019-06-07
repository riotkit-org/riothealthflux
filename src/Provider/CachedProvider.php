<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Doctrine\Common\Cache\Cache;

class CachedProvider implements ServerUptimeProvider
{
    /**
     * @var ServerUptimeProvider $provider
     */
    private $provider;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var string $cacheId
     */
    private $cacheId;

    /**
     * @var int $cacheLifeTime
     */
    private $cacheLifeTime;

    /**
     * @var bool
     */
    private $readFromCacheIfAvailable;

    public function __construct(
        ServerUptimeProvider $provider,
        Cache $cache,
        string $cacheId,
        int $cacheLifeTime = 60,
        bool $readFromCacheIfAvailable = true)
    {
        $this->provider      = $provider;
        $this->cache         = $cache;
        $this->cacheId       = $cacheId;
        $this->cacheLifeTime = $cacheLifeTime;
        $this->readFromCacheIfAvailable = $readFromCacheIfAvailable;
    }

    /**
     * @inheritdoc
     */
    public function canHandle(string $url): bool
    {
        return $this->provider->canHandle($url);
    }

    /**
     * @inheritdoc
     */
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array
    {
        $cacheId = $this->createCacheIdForUrl($url);

        if ($this->readFromCacheIfAvailable && $this->cache->contains($cacheId)) {
            return $this->cache->fetch($cacheId);
        }

        $data = $this->provider->handle($url, $proxyAddress, $proxyAuth);

        if (!$data) {
            return $data;
        }

        $this->cache->save($cacheId, $data, $this->cacheLifeTime);
        return $data;
    }

    private function createCacheIdForUrl(string $url): string
    {
        return $this->cacheId . hash('sha256', $url);
    }
}
