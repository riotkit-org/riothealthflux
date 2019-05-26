<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * @codeCoverageIgnore
 */
class DummyProvider implements ServerUptimeProvider
{
    /**
     * @var Node[] $results
     */
    private $results;

    /**
     * @var bool $canHandle
     */
    private $canHandle;

    public function __construct(array $results, bool $canHandle = true)
    {
        $this->results = $results;
        $this->canHandle = $canHandle;
    }

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
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array
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
