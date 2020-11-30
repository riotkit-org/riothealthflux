<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Console;

use Riotkit\UptimeAdminBoard\Factory\UrlFactory;
use Riotkit\UptimeAdminBoard\Persistence\PersistenceInterface;
use Riotkit\UptimeAdminBoard\Provider\ServerUptimeProvider;
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
         * @var ServerUptimeProvider $provider
         */
        $provider = $this->container->get(ServerUptimeProvider::class);

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
                $persistence->persist($node);
            }
        }

        return 0;
    }
}
