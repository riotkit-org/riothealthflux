<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Riotkit\UptimeAdminBoard\Entity\Node;

interface ServerUptimeProvider
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
     * @param string $proxyAddress
     * @param string $proxyAuth
     *
     * @return Node[]
     */
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array;
}
