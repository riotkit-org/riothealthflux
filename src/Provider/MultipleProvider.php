<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Provider;

use Psr\Log\LoggerInterface;

class MultipleProvider implements ServerUptimeProvider
{
    /**
     * @var ServerUptimeProvider[] $providers
     */
    private $providers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $providers, LoggerInterface $logger)
    {
        $this->providers = $providers;
        $this->logger    = $logger;
    }

    /**
     * @inheritdoc
     */
    public function canHandle(string $url): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->canHandle($url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function handle(string $url, string $proxyAddress = '', string $proxyAuth = ''): array
    {
        foreach ($this->providers as $provider) {
            try {
                if ($provider->canHandle($url)) {
                    return $provider->handle($url, $proxyAddress, $proxyAuth);
                }
            } catch (\Exception $exception) {
                $this->logger->critical(
                    'Cannot handle url "' . $url . '". Error: ' . $exception->getMessage(),
                    ['exception' => $exception]
                );
            }
        }

        return [];
    }
}
