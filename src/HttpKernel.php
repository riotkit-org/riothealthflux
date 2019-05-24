<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wolnosciowiec\UptimeAdminBoard\Controller\DashboardController;
use Wolnosciowiec\UptimeAdminBoard\Controller\ErrorController;

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
        try {
            $response = $this->controller->handle($request);

        } catch (\Throwable $exception) {
            $response = $this->errorController->handle($exception);
        }

        return $response;
    }
}
