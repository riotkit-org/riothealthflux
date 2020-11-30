<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Entity;

/**
 * Represents a server node that is UP or DOWN
 */
class Node implements \JsonSerializable, \Stringable
{
    public const STATUS_UNKNOWN = null;
    public const STATUS_UP      = true;
    public const STATUS_DOWN    = false;

    public function __construct(
        private string $name, private string $checkedBy, private ?bool $status = null,
        private string $url = '', private ?string $actionTime = null
    ) { }


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
            'status' => $this->getStatus()
        ];
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
