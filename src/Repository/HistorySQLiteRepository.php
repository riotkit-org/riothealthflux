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
     * @var bool
     */
    private $canExposeUrls;

    public function __construct(string $path, bool $canExposeUrls)
    {
        $this->conn           = new \PDO('sqlite:' . $path);
        $this->canExposeUrls  = $canExposeUrls;
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
                    $this->q('SELECT * FROM nodes_history ORDER BY checked_by ASC, action_time DESC')->fetchAll(\PDO::FETCH_ASSOC)
                )
            )
        );
    }

    public function findFailingCount(): int
    {
        $result = $this->q('
            SELECT COUNT(id) FROM nodes_history 
            WHERE is_current = true AND status = false;
        ');

        return (int) $result->fetchColumn();
    }

    public function findCurrentCountByStatus(): array
    {
        $result = $this->q('
            SELECT COUNT(id), status FROM nodes_history 
            WHERE is_current = true
            GROUP BY status;
        ');

        $byStatus = [
            'success' => 0,
            'failing' => 0
        ];
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($data as $row) {
            if ($row['status'] == "1") {
                $byStatus['success'] = $row['COUNT(id)'];
                continue;
            }

            $byStatus['failing'] = $row['COUNT(id)'];
        }

        return $byStatus;
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
            $row['action_time'],
            $this->canExposeUrls
        );
    }

    /**
     * @param Node[] $nodes
     *
     * @return NodeHistoryCollection[]
     */
    private function group(array $nodes): array
    {
        $byNodeAndTime = [];

        foreach ($nodes as $node) {
            $byNodeAndTime[$node->getCheckId()][$node->getTime()->getTimestamp()] = $node;
        }

        $groups = [];

        foreach ($byNodeAndTime as $index => $group) {
            $groups[$index] = new NodeHistoryCollection($group);
        }

        return $groups;
    }
}
