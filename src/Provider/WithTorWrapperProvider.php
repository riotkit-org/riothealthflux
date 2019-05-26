<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Service\TORProxyHandler;

/**
 * @codeCoverageIgnore
 */
class WithTorWrapperProvider implements ServerUptimeProvider
{
    /**
     * @var ServerUptimeProvider $provider
     */
    private $provider;

    /**
     * @var TORProxyHandler $torHandler
     */
    private $torHandler;

    public function __construct(ServerUptimeProvider $provider, TORProxyHandler $proxyHandler)
    {
        $this->provider   = $provider;
        $this->torHandler = $proxyHandler;
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
        $this->torHandler->onRequestHandle();
        return $this->provider->handle($url, $proxyAddress, $proxyAuth);
    }
}
