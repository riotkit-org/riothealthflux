<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Wolnosciowiec\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;

class DashboardController
{
    /**
     * @var ShowServicesAvailabilityAction $handler
     */
    private $handler;

    /**
     * @var Environment $twig
     */
    private $twig;

    public function __construct(ShowServicesAvailabilityAction $action, Environment $twig)
    {
        $this->handler  = $action;
        $this->twig     = $twig;
    }

    /**
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(): Response
    {
        return new Response(
            $this->twig->render('dashboard.html.twig', $this->handler->handle())
        );
    }
}
