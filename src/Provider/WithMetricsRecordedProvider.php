<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Provider;

use Wolnosciowiec\UptimeAdminBoard\Repository\HistoryRepository;

/**
 * @codeCoverageIgnore
 */
class WithMetricsRecordedProvider implements ServerUptimeProvider
{
    /**
     * @var ServerUptimeProvider $provider
     */
    private $provider;

    /**
     * @var HistoryRepository
     */
    private $repository;

    public function __construct(ServerUptimeProvider $provider, HistoryRepository $repository)
    {
        $this->provider   = $provider;
        $this->repository = $repository;
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
        $resolved = $this->provider->handle($url, $proxyAddress, $proxyAuth);

        foreach ($resolved as $node) {
            $this->repository->persist($node);
        }

        return $resolved;
    }
}
