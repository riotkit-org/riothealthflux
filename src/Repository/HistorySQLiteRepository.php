<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Repository;

use Wolnosciowiec\UptimeAdminBoard\Entity\Node;

class HistorySQLiteRepository implements HistoryRepository
{
    /**
     * @var \PDO
     */
    private $conn;

    public function __construct(string $path)
    {
        $this->conn = new \PDO('sqlite:' . $path);
    }

    public function persist(Node $node): void
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO nodes_history (id, name, url, status, action_time) 
             VALUES (null, :name, :status, :url, :action_time);'
        );

        if (!$stmt) {
            throw new \Exception('Cannot prepare a statement');
        }

        $stmt->execute([
            'name'        => $node->getName(),
            'status'      => $node->getStatus(),
            'url'         => $node->getUrl(),
            'action_time' => \date('Y-m-d H:i:s')
        ]);
    }
}
