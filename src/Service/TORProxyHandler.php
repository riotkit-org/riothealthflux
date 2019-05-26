<?php declare(strict_types=1);

namespace Riotkit\UptimeAdminBoard\Service;

use Riotkit\UptimeAdminBoard\Exception\TorInvalidResponseError;

/**
 * Adds a hook that handles the proxy eg. IP re-assignment on every call
 * to allow to keep the anonymity if using multiple accounts at the same provider
 */
class TORProxyHandler
{
    /**
     * @var string $proxyAddress
     */
    private $proxyAddress;

    /**
     * @var int $torManagementPort
     */
    private $torManagementPort;

    /**
     * @var string $torPassword
     */
    private $torPassword;

    public function __construct(
        string $proxyAddress,
        int $torManagementPort,
        string $torPassword)
    {
        $this->proxyAddress      = trim($proxyAddress, ' "\'');
        $this->torManagementPort = $torManagementPort;
        $this->torPassword       = $torPassword;
    }

    /**
     * @throws TorInvalidResponseError
     */
    public function onRequestHandle(): void
    {
        if ($this->torManagementPort && $this->proxyAddress) {
            $this->handleTorAddressChange();
        }
    }

    protected function createSocketConnection()
    {
        return fsockopen(parse_url($this->proxyAddress, PHP_URL_HOST), $this->torManagementPort, $errNo, $errStr, 30);
    }

    /**
     * @throws TorInvalidResponseError
     */
    private function handleTorAddressChange(): void
    {
        $fp = $this->createSocketConnection();

        if ($this->torPassword) {
            fwrite($fp, 'AUTHENTICATE "' . $this->torPassword . "\"\r\n");
            $this->assertValidTorResponse(
                $fp,
                'Invalid authentication response from TOR authentication daemon',
                TorInvalidResponseError::INVALID_AUTHENTICATION
            );
        }

        fwrite($fp, "SIGNAL NEWNYM\r\n");
        $this->assertValidTorResponse(
            $fp,
            'Cannot change IP address, TOR rejected request',
            TorInvalidResponseError::CANNOT_CHANGE_IP_ADDRESS
        );

        fclose($fp);
    }

    /**
     * @param resource $fileHandler
     * @param string   $message
     * @param int      $exceptionCode
     *
     * @throws TorInvalidResponseError
     */
    private function assertValidTorResponse($fileHandler, string $message, int $exceptionCode): void
    {
        $response = fread($fileHandler, 1024);

        if (strpos($response, '250 OK') === false) {
            fclose($fileHandler);

            throw new TorInvalidResponseError($message . ', details: ' . json_encode((string) $response), $exceptionCode);
        }
    }
}
