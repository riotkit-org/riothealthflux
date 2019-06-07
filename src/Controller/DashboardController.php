<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Riotkit\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;

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

    /**
     * @var string
     */
    private $template;

    public function __construct(ShowServicesAvailabilityAction $action, Environment $twig, string $template)
    {
        $this->handler  = $action;
        $this->twig     = $twig;
        $this->template = $template;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request): Response
    {
        $data = $this->handler->handle();

        if ($request->getPathInfo() === '/api') {
            return new JsonResponse(json_encode($data, JSON_PRETTY_PRINT), 200, [], true);
        }

        return new Response(
            $this->twig->render($this->template, $data)
        );
    }
}
