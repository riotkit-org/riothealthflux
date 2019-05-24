<?php declare(strict_types=1);

namespace Tests\Wolnosciowiec\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;
use Twig\Environment;
use Wolnosciowiec\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;
use Wolnosciowiec\UptimeAdminBoard\Component\Config;
use Wolnosciowiec\UptimeAdminBoard\Controller\DashboardController;
use Wolnosciowiec\UptimeAdminBoard\Provider\DummyProvider;
use Wolnosciowiec\UptimeAdminBoard\ValueObject\Node;

/**
 * @see DashboardController
 */
class DashboardControllerTest extends TestCase
{
    /**
     * @see DashboardController::handle()
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function test_renders_template_properly(): void
    {
        $kernel = $this->createKernel();

        $handler = new ShowServicesAvailabilityAction(
            new DummyProvider([
                new Node('iwa-ait.org',    Node::STATUS_UP, 'http://iwa-ait.org'),
                new Node('zsp.net.pl',     Node::STATUS_UP, 'http://zsp.net.pl'),
                new Node('solfed.org.uk',  Node::STATUS_UP, 'http://www.solfed.org.uk/'),
                new Node('cnt.es',         Node::STATUS_UP, 'http://www.cnt.es/'),
                new Node('priamaakcia.sk', Node::STATUS_UP, 'http://www.priamaakcia.sk/')
            ]),
            $kernel->getContainer()->get(Config::class)
        );

        $controller = new DashboardController($handler, $kernel->getContainer()->get(Environment::class));
        $response = $controller->handle(new Request());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('iwa-ait.org', $response->getContent());
        $this->assertContains('zsp.net.pl', $response->getContent());
        $this->assertContains('solfed.org.uk', $response->getContent());
        $this->assertContains('cnt.es', $response->getContent());
        $this->assertContains('priamaakcia.sk', $response->getContent());
    }
}
