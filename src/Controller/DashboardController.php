<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
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
     * @param Request $request
     *
     * @return Response
     * 
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        return new Response(
            $this->twig->render('dashboard.html.twig', $this->handler->handle())
        );
    }
}
