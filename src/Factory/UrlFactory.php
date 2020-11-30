<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Factory;

class UrlFactory
{
    private array $urls;

    public function __construct(array $urls)
    {
        $this->urls = $urls;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }
}
