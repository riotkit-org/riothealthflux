<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Provider;

use Riotkit\HealthFlux\DTO\Node;

interface ServerUptimeProviderInterface
{
    /**
     * Tells if is able to handle the URL
     *
     * @param string $url
     *
     * @return bool
     */
    public function canHandle(string $url): bool;

    /**
     * @param string $url
     *
     * @return Node[]
     */
    public function handle(string $url): array;
}
