<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

class MultipleProvider implements ServerUptimeProvider
{
    /**
     * @var ServerUptimeProvider[] $providers
     */
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function canHandle(string $url): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->canHandle($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array
    {
        foreach ($this->providers as $provider) {
            if ($provider->canHandle($url)) {
                return $provider->handle($url, $proxyAddress, $proxyAuth);
            }
        }

        return [];
    }
}
