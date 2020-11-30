<?php declare(strict_types=1);

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
    'providers'           => get_env_array('RIOT_PROVIDERS', []),
    'log_path'            => get_env_string('RIOT_LOG_PATH', './uptime-admin-board.log'),
    'influxdb_url'        => get_env_string('INFLUXDB_URL', 'http://localhost:8086'),
    'influxdb_token'      => get_env_string('INFLUXDB_TOKEN', ''),
    'influxdb_bucket'     => get_env_string('INFLUXDB_BUCKET', ''),
    'influxdb_org'        => get_env_string('INFLUXDB_ORG', '')
]);
