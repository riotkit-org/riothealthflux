<?php declare(strict_types=1);

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Riotkit\HealthFlux\Component\Config;
use Riotkit\HealthFlux\Factory\UrlFactory;
use Riotkit\HealthFlux\Persistence\InfluxDBPersistence;
use Riotkit\HealthFlux\Persistence\PersistenceInterface;
use Riotkit\HealthFlux\Provider\InfracheckProvider;
use Riotkit\HealthFlux\Provider\MultipleProvider;
use Riotkit\HealthFlux\Provider\ServerUptimeProviderInterface;
use Riotkit\HealthFlux\Provider\UptimeRobotProvider;

return [

    //
    // Infrastructure
    //
    
    LoggerInterface::class => static function (Config $config) {
        $logger = new Logger('riothealthflux');

        if (PHP_SAPI === 'cli') {
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
        }

        if ($config->get('log_path', '')) {
            $logger->pushHandler(new StreamHandler($config->get('log_path'), Logger::INFO));
        }

        return $logger;
    },

    PersistenceInterface::class => static function (Container $container) {
        return $container->get(InfluxDBPersistence::class);
    },

    InfluxDBPersistence::class => static function (Config $config) {
        return new InfluxDBPersistence(
            $config->get('influxdb_url'),
            $config->get('influxdb_measurement_name')
        );
    },


    //
    // Application
    //

    // STARTS: Provider chain
    ServerUptimeProviderInterface::class => static function (Container $container) {
        return $container->get(MultipleProvider::class);
    },

    MultipleProvider::class => static function (Container $container) {
        return new MultipleProvider(
            [
                $container->get(InfracheckProvider::class),
                $container->get(UptimeRobotProvider::class),
            ],
            $container->get(LoggerInterface::class)
        );
    },

    UptimeRobotProvider::class => static function () {
        return new UptimeRobotProvider();
    },

    // ENDS: Provider chain

    UrlFactory::class => static function (Config $config) {
        return new UrlFactory($config->get('providers'));
    },

    Config::class => static function () {
        return new Config(require __DIR__ . '/../../config.php');
    }

    // ... the rest of services are not configured due to their simple dependencies
    //     that are resolved automatically by autowiring in the DI container
];
