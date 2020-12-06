<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Component;

use Riotkit\HealthFlux\DTO\Node;
use Tests\TestCase;

/**
 * @see Node
 */
class NodeTest extends TestCase
{
    /**
     * @see Node::isUp()
     */
    public function testIsUp(): void
    {
        $upNode = new Node(
            name: 'test', checkedBy: 'PHPUnit', status: true,
            url: 'http://127.0.0.1'
        );

        $downNode = new Node(
            name: 'test', checkedBy: 'PHPUnit', status: false,
            url: 'http://127.0.0.1'
        );

        $this->assertTrue($upNode->isUp());
        $this->assertFalse($downNode->isUp());
    }
}
