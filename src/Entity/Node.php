<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Entity;

/**
 * Represents a server node that is UP or DOWN
 */
class Node
{
    public const STATUS_UNKNOWN = null;
    public const STATUS_UP      = true;
    public const STATUS_DOWN    = false;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $url
     */
    private $url;

    /**
     * @var null|bool $status
     */
    private $status;

    public function __construct(string $name, ?bool $status = null, string $url = '')
    {
        $this->name   = $name;
        $this->status = $status;
        $this->url    = $url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isUp(): bool
    {
        return $this->status === self::STATUS_UP;
    }
}
