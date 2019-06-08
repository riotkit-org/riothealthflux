<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Entity;

/**
 * Represents a server node that is UP or DOWN
 */
class Node implements \JsonSerializable
{
    public const STATUS_UNKNOWN = null;
    public const STATUS_UP      = true;
    public const STATUS_DOWN    = false;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var null|bool
     */
    private $status;

    /**
     * @var string
     */
    private $checkedBy;

    /**
     * @var string
     */
    private $actionTime;

    /**
     * @var bool
     */
    private $canExposeUrl;

    public function __construct(
        string $name, string $checkedBy, ?bool $status = null,
        string $url = '', string $time = null, bool $canExposeUrl = false)
    {
        $this->name       = $name;
        $this->status     = $status;
        $this->url        = $url;
        $this->checkedBy  = $checkedBy;
        $this->actionTime = $time;
        $this->canExposeUrl = $canExposeUrl;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function isUp(): bool
    {
        return $this->status === self::STATUS_UP;
    }

    public function isDown(): bool
    {
        return $this->status === self::STATUS_DOWN;
    }

    public function getCheckedBy(): string
    {
        return $this->checkedBy;
    }

    public function getCheckId(): string
    {
        return hash('sha256', $this->name . '_' . $this->url);
    }

    /**
     * @return \DateTimeImmutable
     *
     * @throws \Exception
     */
    public function getTime(): \DateTimeImmutable
    {
        if (!$this->actionTime) {
            return new \DateTimeImmutable();
        }

        return new \DateTimeImmutable($this->actionTime);
    }

    public function jsonSerialize(): array
    {
        return [
            'name'   => $this->getName(),
            'id'     => $this->getCheckId(),
            'status' => $this->getStatus(),
            'url'    => $this->canExposeUrl ? $this->url : ''
        ];
    }
}
