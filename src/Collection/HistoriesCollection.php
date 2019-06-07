<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Riotkit\UptimeAdminBoard\Entity\Node;

/**
 * Collection of NodeHistoryCollection which is a collection of Node
 */
class HistoriesCollection extends ArrayCollection
{
    /**
     * @see NodeHistoryCollection::getFailingScore()
     *
     * @param int $max
     *
     * @return array
     */
    public function findMostlyFailingNodes(int $max = 15): array
    {
        return array_filter(
            $this->getTopNodesByScore('score', 'getFailingScore', $max, true),
            static function (array $element) {
                return $element['score'] > 0;
            }
        );
    }

    /**
     * @see NodeHistoryCollection::wasRecentlyFixed()
     *
     * @param int $max
     *
     * @return array
     */
    public function findRecentlyFixed(int $max = 15): array
    {
        return $this->getFilteredNodesBy('wasRecentlyFixed', $max);
    }

    public function findCountPerHour(): array
    {
        return \array_map(
            static function (array $time) {
                return [
                    'up'   => \count($time[true] ?? []),
                    'down' => \count($time[false] ?? [])
                ];
            },
            $this->findPerHour()
        );
    }

    public function findPerHour(): array
    {
        /**
         * @var Node[] $allJoinedNodes
         */
        $allJoinedNodes = $this->map(
            static function (NodeHistoryCollection $collection) {
                return $collection->toArray();
            }
        )->toArray();

        if ($allJoinedNodes) {
            $allJoinedNodes = \array_merge(...\array_values($allJoinedNodes));
        }

        // sort ascending, so the next step will leave in $indexedByNodeAndHour only last node
        usort($allJoinedNodes, static function (Node $a, Node $b) {
            return $a->getTime()->getTimestamp() <=> $b->getTime()->getTimestamp();
        });

        $indexed = [];

        // initially index
        foreach ($allJoinedNodes as $node) {
            $indexed[$node->getTime()->format('Y-m-d_H')][$node->getStatus()][$node->getCheckId()][$node->getTime()->getTimestamp()] = $node;
        }

        // leave only last check in each hour
        foreach ($indexed as &$time) {
            foreach ($time as &$status) {
                foreach ($status as &$checks) {
                    krsort($checks);
                    $checks = \array_values($checks)[0];
                }
                unset($checks);
            }
            unset($status);
        }
        unset($time);

        return $indexed;
    }

    /**
     * @see NodeHistoryCollection::getFailuresInLast24Hours()
     *
     * @param int $max
     *
     * @return array
     */
    public function findMostUnstableInLast24Hours(int $max = 15): array
    {
        return array_filter(
            $this->getTopNodesByScore('count', 'getFailuresInLast24Hours', $max, true),
            static function (array $element) {
                return $element['count'] > 0;
            }
        );
    }

    private function getFilteredNodesBy(string $getter, int $maxCount): array
    {
        $nodes = $this
            ->filter(
                function (NodeHistoryCollection $collection) use ($getter) {
                    return $collection->$getter();
                }
            )
            ->map(
                function (NodeHistoryCollection $collection) use ($getter) {
                    return \array_merge($collection->getNode()->jsonSerialize(), [$getter => $collection->$getter()]);
                }
            )
            ->toArray();

        return \array_slice($nodes, 0, $maxCount);
    }

    /**
     * @param string $field   Field to return in the result array
     * @param string $getter  Getter in the NodeHistoryCollection
     * @param int $maxCount   Maximum amount of results
     * @param bool $reverse   Should the results be inverted? We are searching for top or last?
     *
     * @return Node[]
     */
    private function getTopNodesByScore(string $field, string $getter, int $maxCount, bool $reverse): array
    {
        $items = \array_map(
            static function (NodeHistoryCollection $collection) use ($field, $getter) {
                return [
                    'node'  => $collection->getNode(),
                    $field => $collection->$getter()
                ];
            },
            $this->toArray()
        );

        \usort($items, function (array $a, array $b) use ($field) {
            return $a[$field] <=> $b[$field];
        });

        if ($reverse) {
            $items = \array_reverse($items);
        }

        return \array_slice($items, 0, $maxCount);
    }
}
