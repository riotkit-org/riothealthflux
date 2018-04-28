<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Provider;

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

    public function __construct(ServerUptimeProvider $provider, Cache $cache, string $cacheId, int $cacheLifeTime = 60)
    {
        $this->provider      = $provider;
        $this->cache         = $cache;
        $this->cacheId       = $cacheId;
        $this->cacheLifeTime = $cacheLifeTime;
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
        if ($this->cache->contains($this->cacheId)) {
            return $this->cache->fetch($this->cacheId);
        }

        $data = $this->provider->handle($url, $proxyAddress, $proxyAuth);

        if (!$data) {
            return $data;
        }

        $this->cache->save($this->cacheId, $data, $this->cacheLifeTime);
        return $data;
    }
}
