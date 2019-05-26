<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Provider;

use Doctrine\Common\Cache\ArrayCache;
use Tests\TestCase;
use Riotkit\UptimeAdminBoard\Provider\CachedProvider;
use Riotkit\UptimeAdminBoard\Provider\DummyProvider;
use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * @see CachedProvider
 */
class CachedProviderTest extends TestCase
{
    /**
     * @see CachedProvider::handle()
     */
    public function test_cache_expires_after_given_time_and_returns_fresh_results(): void
    {
        $provider       = new DummyProvider([new Node('FIRST', Node::STATUS_UP, 'http://zsp.net.pl')]);
        $cachedProvider = new CachedProvider($provider, new ArrayCache(), 'test', 1);

        $firstResult = $cachedProvider->handle('DummyProvider://test');                         // 1. test
        $provider->setResults([new Node('SECOND', Node::STATUS_UP, 'http://zsp.net.pl')]);      // 2. insert new fake data
        $secondResultFromCache = $cachedProvider->handle('DummyProvider://test');               // 3. test from cache
        sleep(2);                                                                               // 4. wait until cache expiration
        $thirdResultFreshAfterExpirationTime = $cachedProvider->handle('DummyProvider://test'); // 5. test, should be returned fresh data (new fake data)

        $this->assertSame($firstResult, $secondResultFromCache, 'The result is expected to be from cache');
        $this->assertNotSame($firstResult, $thirdResultFreshAfterExpirationTime, 'The third result is expected to be fresh');
    }
}
