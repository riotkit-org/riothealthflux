<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * Collection of Node
 *
 * @method Node[] toArray()
 * @method Node last()
 */
class NodeHistoryCollection extends ArrayCollection
{
    public function getNode(): Node
    {
        return $this->first();
    }

    public function getFailingScore(): float
    {
        $stat = [true => 0, false => 0];

        foreach ($this->toArray() as $node) {
            $stat[$node->getStatus()] = $node;
        }

        return ($stat[false] / ($stat[true] + $stat[false])) * 100;
    }

    /**
     * Tells if the last check is successful, but any of previous X checks were failing
     * That would mean, that the check was recently resolved
     *
     * @param int $maxChecks
     *
     * @return bool
     */
    public function wasRecentlyFixed(int $maxChecks = 5): bool
    {
        $last = $this->last();

        if (!$last->isUp()) {
            return false;
        }

        // check if any of previous X checks were failing

        /** @var Node[] $lastChecks */
        $lastChecks = \array_slice($this->toArray(), ($maxChecks + 1) * -1, $maxChecks, true);

        if (!$lastChecks) {
            return false;
        }

        foreach ($lastChecks as $check) {
            if (!$check->isUp()) {
                return true;
            }
        }

        return false;
    }

    public function getFailuresInLast24Hours(): int
    {
        $startTime = new \DateTime();
        $startTime = $startTime->modify('-24 hours');

        return \count(
            $this->filter(
                static function (Node $node) use ($startTime) {
                    return $node->isDown() && $node->getTime() >= $startTime;
                }
            )
        );
    }
}
