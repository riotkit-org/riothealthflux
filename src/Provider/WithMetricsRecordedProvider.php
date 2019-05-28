<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Repository\HistoryRepository;

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

    /**
     * @var int
     */
    private $maxDaysToKeepHistory;

    public function __construct(ServerUptimeProvider $provider, HistoryRepository $repository, int $maxDaysToKeepHistory)
    {
        $this->provider             = $provider;
        $this->repository           = $repository;
        $this->maxDaysToKeepHistory = $maxDaysToKeepHistory;
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

        if ($this->maxDaysToKeepHistory > 0) {
            $this->repository->removeOlderThanDays($this->maxDaysToKeepHistory);
        }

        return $resolved;
    }
}
