<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Riotkit\UptimeAdminBoard\Controller\DashboardController;
use Riotkit\UptimeAdminBoard\Controller\ErrorController;

/**
 * Http Kernel decides which endpoint to execute
 * When the application will grow then there may be a router
 * instead of the direct controller selection
 */
class HttpKernel
{
    /**
     * @var DashboardController
     */
    private $controller;

    /**
     * @var ErrorController
     */
    private $errorController;

    public function __construct(DashboardController $controller, ErrorController $errorController)
    {
        $this->controller      = $controller;
        $this->errorController = $errorController;
    }

    public function handle(Request $request): Response
    {
        if (strpos($request->headers->get('User-Agent'), 'curl/') !== false) {
            return new Response('OK');
        }

        try {
            $response = $this->controller->handle($request);

        } catch (\Throwable $exception) {
            $response = $this->errorController->handle($exception);
        }

        return $response;
    }
}
