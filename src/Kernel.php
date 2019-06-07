<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard;

use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionFile;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

class Kernel
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct()
    {
        $this->loadDependencyInjection();
    }

    private function loadDependencyInjection()
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);
        $builder->useAutowiring(true);
        $builder->addDefinitions(new DefinitionFile(__DIR__ . '/DependencyInjection/services.php'));
        $this->container = $builder->build();
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Request $request
     *
     * @return Response
     */
    public function executeRequest(Request $request): Response
    {
        return $this->container->get(HttpKernel::class)->handle($request);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Response $response
     */
    public function emitResponse(Response $response)
    {
        $response->send();
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
