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
    public function findMostlyFailingNodes(int $max = 10): array
    {
        return $this->getTopNodesByScore('score', 'getFailingScore', $max, true);
    }

    /**
     * @see NodeHistoryCollection::wasRecentlyFixed()
     *
     * @param int $max
     *
     * @return array
     */
    public function findRecentlyFixed(int $max = 10): array
    {
        return $this->getFilteredNodesBy('wasRecentlyFixed', $max);
    }

    /**
     * @see NodeHistoryCollection::getFailuresInLast24Hours()
     *
     * @param int $max
     *
     * @return array
     */
    public function findMostUnstableInLast24Hours(int $max = 10): array
    {
        return $this->getTopNodesByScore('count', 'getFailuresInLast24Hours', $max, true);
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
                function (NodeHistoryCollection $collection) {
                    return $collection->getNode();
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
