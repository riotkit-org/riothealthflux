<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Repository;

use Riotkit\UptimeAdminBoard\Collection\HistoriesCollection;
use Riotkit\UptimeAdminBoard\Collection\NodeHistoryCollection;
use Riotkit\UptimeAdminBoard\Entity\Node;

class HistorySQLiteRepository implements HistoryRepository
{
    /**
     * @var \PDO
     */
    private $conn;

    /**
     * @var Node[]
     */
    private $all;

    public function __construct(string $path)
    {
        $this->conn = new \PDO('sqlite:' . $path);
    }

    public function persist(Node $node): void
    {
        $this->markPreviousAsNotCurrent($node);

        $params = [
            'name'         => $node->getName(),
            'status'       => (int) $node->getStatus(),
            'url'          => $node->getUrl(),
            'action_time'  => \date('Y-m-d H:i:s'),
            'checked_by'   => $node->getCheckedBy(),
            'check_id'     => $node->getCheckId()
        ];

        $this->q(
            'INSERT INTO nodes_history (id, name, url, status, action_time, checked_by, check_id, is_current) 
             VALUES (null, :name, :url, :status, :action_time, :checked_by, :check_id, true);',
            $params
        );
    }

    public function findAllGrouped(): HistoriesCollection
    {
        return new HistoriesCollection(
            $this->group(
                $this->toObjects(
                    $this->q('SELECT * FROM nodes_history ORDER BY checked_by ASC')->fetchAll()
                )
            )
        );
    }

    public function findCountPerHour(): array
    {
        $result = $this->q(
            'SELECT status, strftime("%d-%m-%Y %H", action_time) as hour FROM nodes_history
             GROUP BY status, strftime("%d-%m-%Y %H", action_time), id'
        );

        $raw = $result->fetchAll();

        var_dump($raw);
        die();

        $map = \array_map(
            static function (array $row) {
                return [
                    'date'   => $row['hour'],
                    'count'  => $row['COUNT(id)'],
                    'status' => (bool) $row['status']
                ];
            },
            $raw
        );

        \usort($map, static function (array $a, array $b) {
            return \strtotime($a['date'] . ':00:00') <=> \strtotime($b['date'] . ':00:00');
        });

        return [
            'successes' => \array_filter($map, function (array $entry) { return $entry['status']; }),
            'failures'  => \array_filter($map, function (array $entry) { return !$entry['status']; })
        ];
    }

    public function findFailingCount(): int
    {
        $result = $this->q('
            SELECT COUNT(id) FROM nodes_history 
            WHERE is_current = true AND status = false;
        ');

        return (int) $result->fetchColumn();
    }

    public function findSuccessCount(): int
    {
        $result = $this->q('
            SELECT COUNT(id) FROM nodes_history 
            WHERE is_current = true AND status = true;
        ');

        return (int) $result->fetchColumn();
    }

    public function removeOlderThanDays(int $maxDays): void
    {
        $this->q(
            'DELETE FROM nodes_history WHERE action_time < date("now", :action_time);',
            ['action_time' => '-' . $maxDays . ' days']
        );
    }

    private function markPreviousAsNotCurrent(Node $node): void
    {
        $this->q(
            'UPDATE nodes_history SET is_current = false WHERE check_id = :check_id;',
            ['check_id' => $node->getCheckId()]
        );
    }

    private function q(string $query, array $params = []): \PDOStatement
    {
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            throw new \Exception('SQLite error: ' . \json_encode($this->conn->errorInfo()));
        }

        $stmt->execute($params);

        return $stmt;
    }

    private function toObjects(array $rows): array
    {
        return array_map(
            function (array $row) {
                return $this->toObject($row);
            },
            $rows
        );
    }

    private function toObject(array $row): Node
    {
        return new Node(
            $row['name'],
            $row['checked_by'],
            (bool) $row['status'],
            $row['url'],
            $row['action_time']
        );
    }

    /**
     * @param Node[] $nodes
     *
     * @return NodeHistoryCollection[]
     */
    private function group(array $nodes): array
    {
        $grouped = [];

        foreach ($nodes as $node) {
            $grouped[$node->getCheckId()][$node->getTime()->getTimestamp()] = $node;
        }

        foreach ($grouped as &$group) {
            ksort($group);
        }

        return array_map(
            function (array $group) {
                return new NodeHistoryCollection($group);
            },
            $grouped
        );
    }
}
