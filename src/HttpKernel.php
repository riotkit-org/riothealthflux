<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wolnosciowiec\UptimeAdminBoard\Controller\DashboardController;

/**
 * Http Kernel decides which endpoint to execute
 * When the application will grow then there may be a router
 * instead of the direct controller selection
 */
class HttpKernel
{
    /**
     * @var DashboardController $controller
     */
    private $controller;

    public function __construct(DashboardController $controller)
    {
        $this->controller = $controller;
    }

    public function handle(Request $request): Response
    {
        return $this->controller->handle($request);
    }
}
