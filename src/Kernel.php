<?php declare(strict_types=1);

namespace Wolnosciowiec\UptimeAdminBoard;

use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionFile;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
     */
    public function emitResponse(Request $request)
    {
        $response = $this->container->get(HttpKernel::class)->handle($request);
        $response->send();
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
