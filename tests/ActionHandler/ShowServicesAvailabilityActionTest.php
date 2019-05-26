<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\ActionHandler;

use Tests\TestCase;
use Riotkit\UptimeAdminBoard\ActionHandler\ShowServicesAvailabilityAction;

/**
 * @see ShowServicesAvailabilityAction
 */
class ShowServicesAvailabilityActionTest extends TestCase
{
    /**
     * @see ShowServicesAvailabilityAction::handle()
     */
    public function test_passes_configuration_properly(): void
    {
        $_ENV['UAB_PROVIDERS'] = '';
        $_ENV['UAB_TITLE']     = 'International Workers Association servers dashboard';
        $_ENV['UAB_CSS']       = 'some.css';

        $response = $this->createKernel()->getContainer()->get(ShowServicesAvailabilityAction::class)->handle();

        $this->assertSame('International Workers Association servers dashboard', $response['title']);
        $this->assertTrue($response['canExposeUrls']);
        $this->assertSame('some.css', $response['css']);
    }
}
