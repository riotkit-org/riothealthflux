<?php declare(strict_types=1);

namespace Riotkit\Console;

use Riotkit\UptimeAdminBoard\Service\Health\StatusProcessingService;
use Riotkit\UptimeAdminBoard\Service\Stats\StatsProcessingService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackgroundProcessCommand extends ConsoleCommand
{
    public function configure()
    {
        $this->setName('background-process');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching statuses...');
        $nodesGrouped = $this->container->get(StatusProcessingService::class)->warmUp();
        $output->writeln('Done');

        $output->writeln('Processing statistics...');
        $this->container->get(StatsProcessingService::class)->warmUp($nodesGrouped);
        $output->writeln('Done');
    }
}
