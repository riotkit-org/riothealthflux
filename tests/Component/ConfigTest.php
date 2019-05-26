<?php declare(strict_types=1);

namespace Tests\Riotkit\UptimeAdminBoard\Component;

use Tests\TestCase;
use Riotkit\UptimeAdminBoard\Component\Config;

/**
 * @see Config
 */
class ConfigTest extends TestCase
{
    /**
     * @see Config::get()
     */
    public function test_get()
    {
        $data   = [
            'test_int'          => 1,
            'test_boolean'      => false,
            'test_null'         => null,
            'test_empty_string' => '',
            'test_string'       => 'http://iwa-ait.org'
        ];
        
        $config = new Config($data);

        foreach ($data as $key => $value) {
            $this->assertSame($value, $config->get($key));
        }

        $this->assertSame('some-default-value', $config->get('non-existing-key', 'some-default-value'));
    }
}
