<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Service;

use Tests\TestCase;
use Riotkit\UptimeAdminBoard\Exception\TorInvalidResponseError;
use Riotkit\UptimeAdminBoard\Service\TORProxyHandler;

class TORProxyHandlerTest extends TestCase
{
    /**
     * @see TORProxyHandler::onRequestHandle()
     */
    public function test_properly_communicates_to_tor(): void
    {
        $mockBuilder = $this->getMockBuilder(TORProxyHandler::class);
        $mockBuilder->setMethods(['createSocketConnection']);
        $mockBuilder->setConstructorArgs(['http://localhost:8000', 8118, 'test123']);

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|TORProxyHandler $mock
         */
        $mock = $mockBuilder->getMock();

        // put a file instead of a socket
        $tempFilePath = tempnam('/tmp', 'torproxyhandlertest');
        file_put_contents($tempFilePath, str_repeat("250 OK\n", 1024));

        $fp = fopen($tempFilePath, 'rb');
        $mock->method('createSocketConnection')->willReturn($fp);

        // execute the action
        $mock->onRequestHandle();

        // no exception means that everything went fine
        $this->assertTrue(true);

        // clean up
        unlink($tempFilePath);
    }

    /**
     * @see TORProxyHandler::onRequestHandle()
     */
    public function test_throws_exception_on_authentication_failure(): void
    {
        $this->expectException(TorInvalidResponseError::class);
        $this->expectExceptionCode(TorInvalidResponseError::INVALID_AUTHENTICATION);

        $mockBuilder = $this->getMockBuilder(TORProxyHandler::class);
        $mockBuilder->setMethods(['createSocketConnection']);
        $mockBuilder->setConstructorArgs(['http://localhost:8000', 8118, 'test123']);

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|TORProxyHandler $mock
         */
        $mock = $mockBuilder->getMock();

        // put a file instead of a socket
        $tempFilePath = tempnam('/tmp', 'torproxyhandlertest');
        file_put_contents($tempFilePath, '400 AUTHENTICATION FAILURE');

        $fp = fopen($tempFilePath, 'rb');
        $mock->method('createSocketConnection')->willReturn($fp);

        // execute the action
        $mock->onRequestHandle();

        // clean up
        unlink($tempFilePath);
    }
}
