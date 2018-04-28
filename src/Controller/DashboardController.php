<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wolnosciowiec\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;
use \Twig_Environment;

class DashboardController
{
    /**
     * @var ShowServicesAvailabilityAction $handler
     */
    private $handler;

    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    public function __construct(ShowServicesAvailabilityAction $action, Twig_Environment $twig)
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
