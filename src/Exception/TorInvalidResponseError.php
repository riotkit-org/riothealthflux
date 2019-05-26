<?php declare(strict_types = 1);

namespace Riotkit\UptimeAdminBoard\Exception;

/**
 * @codeCoverageIgnore
 */
class TorInvalidResponseError extends ApplicationException
{
    public const INVALID_AUTHENTICATION   = 100001;
    public const CANNOT_CHANGE_IP_ADDRESS = 100002;
}
