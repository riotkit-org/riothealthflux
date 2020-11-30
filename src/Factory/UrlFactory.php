<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Factory;

class UrlFactory
{
    public function __construct(private array $urls) { }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function getUrls(): array
    {
        return $this->urls;
    }
}
