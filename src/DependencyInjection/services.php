<?php declare(strict_types=1);

use DI\Container;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\PredisCache;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Predis\Client as PredisClient;
use Psr\Log\LoggerInterface;
use Riotkit\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;
use Riotkit\UptimeAdminBoard\Component\Config;
use Riotkit\UptimeAdminBoard\Controller\DashboardController;
use Riotkit\UptimeAdminBoard\Provider\MultipleProvider;
use Riotkit\UptimeAdminBoard\Provider\ServerUptimeProvider;
use Riotkit\UptimeAdminBoard\Provider\UptimeRobotProvider;
use Riotkit\UptimeAdminBoard\Provider\WithMetricsRecordedProvider;
use Riotkit\UptimeAdminBoard\Provider\WithTorWrapperProvider;
use Riotkit\UptimeAdminBoard\Repository\HistoryRepository;
use Riotkit\UptimeAdminBoard\Repository\HistorySQLiteRepository;
use Riotkit\UptimeAdminBoard\Service\TORProxyHandler;
use Twig\Environment;

return [

    //
    // Infrastructure
    //
    
    Cache::class => static function (Container $container) {
        $config    = $container->get(Config::class);
        $cacheType = $config->get('cache');

        if ($cacheType === 'redis') {
            $cache = new PredisCache(new PredisClient([
                'scheme' => 'tcp',
                'host'   => $config->get('redis_host'),
                'port'   => $config->get('redis_port')
            ]));

            return $cache;
        }

        return new FilesystemCache(__DIR__ . '/../../var/cache/');
    },

    Twig\Environment::class => static function () {
        return new Twig\Environment(
            new Twig\Loader\FilesystemLoader([
                __DIR__ . '/../../templates/',
                __DIR__ . '/../../public/'
            ])
        );
    },

    LoggerInterface::class => static function (Config $config) {
        $logger = new Logger('uptime-admin-board');

        if (PHP_SAPI === 'cli') {
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
        }

        if ($config->get('log_path', '')) {
            $logger->pushHandler(new StreamHandler($config->get('log_path'), Logger::INFO));
        }

        return $logger;
    },


    //
    // Application
    //

    DashboardController::class => static function (Container $container, Config $config) {
        return new DashboardController(
            $container->get(ShowServicesAvailabilityAction::class),
            $container->get(Environment::class),
            $config->get('dynamic_dashboard') ? 'index.html' : 'dashboard.html.twig'
        );
    },

    HistorySQLiteRepository::class => static function (Config $config) {
        return new HistorySQLiteRepository(
            $config->get('db_path'),
            $config->get('expose_url'),
            $config->get('only_hourly_data')
        );
    },

    HistoryRepository::class => static function (Container $container) {
        return $container->get(HistorySQLiteRepository::class);
    },

    // STARTS: Provider chain
    ServerUptimeProvider::class => static function (Container $container) {
        return $container->get(WithMetricsRecordedProvider::class);
    },

    WithTorWrapperProvider::class => static function (Container $container) {
        return new WithTorWrapperProvider(
            $container->get(MultipleProvider::class),
            $container->get(TORProxyHandler::class)
        );
    },

    WithMetricsRecordedProvider::class => static function (Container $container, Config $config) {
        return new WithMetricsRecordedProvider(
            $container->get(WithTorWrapperProvider::class),
            $container->get(HistoryRepository::class),
            $config->get('history_max_days')
        );
    },

    MultipleProvider::class => static function (Container $container) {
        return new MultipleProvider(
            [$container->get(UptimeRobotProvider::class)],
            $container->get(LoggerInterface::class)
        );
    },

    UptimeRobotProvider::class => static function (Config $config) {
        return new UptimeRobotProvider($config->get('expose_url'));
    },

    // ENDS: Provider chain

    TORProxyHandler::class => static function (Container $container) {
        $config = $container->get(Config::class);

        return new TORProxyHandler(
            $config->get('proxy_address', ''),
            $config->get('tor_management_port', 9052),
            $config->get('tor_password', '')
        );
    },

    Config::class => static function () {
        return new Config(require __DIR__ . '/../../config.php');
    }

    // ... the rest of services are not configured due to their simple dependencies
    //     that are resolved automatically by autowiring in the DI container
];
