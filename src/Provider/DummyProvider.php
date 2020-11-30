<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Provider;

use Riotkit\HealthFlux\DTO\Node;

/**
 * @codeCoverageIgnore
 */
class DummyProvider implements ServerUptimeProviderInterface
{
    /**
     * @param Node[] $results
     * @param bool $canHandle
     */
    public function __construct(private array $results, private bool $canHandle = true) { }

    /**
     * @inheritdoc
     */
    public function canHandle(string $url): bool
    {
        return $this->canHandle;
    }

    /**
     * @inheritdoc
     */
    public function handle(string $url): array
    {
        return $this->results;
    }

    /**
     * @param Node[] $results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function setCanHandle(bool $canHandle): void
    {
        $this->canHandle = $canHandle;
    }
}
