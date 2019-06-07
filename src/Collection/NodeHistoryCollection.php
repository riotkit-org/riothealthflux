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
            $stat[$node->getStatus()]++;
        }

        return floor(($stat[false] / ($stat[true] + $stat[false])) * 100);
    }

    /**
     * Tells if the last check is successful, but any of previous X checks were failing
     * That would mean, that the check was recently resolved
     *
     * @param int $maxChecks
     *
     * @return null|int Last check, that was failing
     */
    public function wasRecentlyFixed(int $maxChecks = 50): ?int
    {
        $last = $this->last();

        if (!$last->isUp()) {
            return null;
        }

        // check if any of previous X checks were failing

        /** @var Node[] $lastChecks */
        $lastChecks = \array_slice($this->toArray(), ($maxChecks + 1) * -1, $maxChecks, true);

        if (!$lastChecks) {
            return null;
        }

        $checkNum = 0;

        foreach ($lastChecks as $check) {
            $checkNum++;

            if (!$check->isUp()) {
                return $checkNum;
            }
        }

        return null;
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
