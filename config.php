<?php declare(strict_types=1);

$_ENV['UAB_PROVIDERS'] = 'UptimeRobot://u411998-5becf6fc0d772b8f0d5f201d;UptimeRobot://u627870-a4afa062e18ebc27a353da91';

if (!function_exists('get_env_array')) {
    global $__uab_env;
    $__uab_env = array_merge($_SERVER, $_ENV);

    function get_env_array(string $name, array $default, string $separator = ';') {
        global $__uab_env;

        return array_map(
            function (string $str) { return trim($str); },
            isset($__uab_env[$name]) ? explode($separator, $__uab_env[$name]) : $default
        );
    }

    function get_env_integer(string $name, int $default) {
        global $__uab_env;

        return isset($__uab_env[$name]) ? (int) $__uab_env[$name] : $default;
    }

    function get_env_boolean(string $name, bool $default) {
        global $__uab_env;

        return isset($__uab_env[$name]) ? (bool) $__uab_env[$name] : $default;
    }

    function get_env_string(string $name, string $default) {
        global $__uab_env;

        return isset($__uab_env[$name]) ? (string) $__uab_env[$name] : $default;
    }

    function unescape_env_value($value) {
        if (\is_string($value)) {
            return trim($value, " \n\"'");
        }

        return $value;
    }
}

return array_map('unescape_env_value', [
    'title'               => get_env_string('UAB_TITLE', 'System monitoring'),
    'providers'           => get_env_array('UAB_PROVIDERS', []),
    'css'                 => get_env_string('UAB_CSS', './assets/css/zapatista.css'),
    'cache_id'            => get_env_string('UAB_CACHE_ID', hash('sha256', __DIR__)),
    'cache_ttl'           => get_env_integer('UAB_CACHE_TTL', 1),
    'cache'               => get_env_string('UAB_CACHE', 'file'),
    'redis_host'          => get_env_string('UAB_REDIS_HOST', 'localhost'),
    'redis_port'          => get_env_integer('UAB_REDIS_PORT', 6379),
    'tor_management_port' => get_env_integer('UAB_TOR_MANAGEMENT_PORT', 9052),
    'tor_password'        => get_env_string('UAB_TOR_PASSWORD', ''),
    'proxy_address'       => get_env_string('UAB_PROXY', ''),
    'expose_url'          => get_env_boolean('UAB_EXPOSE_URLS', true)
]);
