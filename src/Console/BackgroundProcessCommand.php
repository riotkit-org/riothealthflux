<?php declare(strict_types=1);

namespace Riotkit\HealthFlux\Console;

use Riotkit\HealthFlux\Factory\UrlFactory;
use Riotkit\HealthFlux\Persistence\PersistenceInterface;
use Riotkit\HealthFlux\Provider\ServerUptimeProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackgroundProcessCommand extends ConsoleCommand
{
    public function configure()
    {
        $this->setName('background-process');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var ServerUptimeProviderInterface $provider
         */
        $provider = $this->container->get(ServerUptimeProviderInterface::class);

        /**
         * @var PersistenceInterface $persistence
         */
        $persistence = $this->container->get(PersistenceInterface::class);

        /**
         * @var UrlFactory $urlFactory
         */
        $urlFactory = $this->container->get(UrlFactory::class);

        foreach ($urlFactory->getUrls() as $url) {
            $output->writeln('Fetching statuses for url=' . $url . '...');

            foreach ($provider->handle($url) as $node) {
                $output->writeln('Writing status for ' . $node);
                $persistence->persist($node);
            }
        }

        return 0;
    }
}
