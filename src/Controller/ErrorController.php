<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ErrorController
{
    /**
     * @var Environment $twig
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param \Throwable $exception
     *
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle(\Throwable $exception): Response
    {
        return new Response(
            $this->twig->render('exception.html.twig', ['exception' => $exception])
        );
    }
}
