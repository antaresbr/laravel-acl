<?php

namespace Antares\Acl\Http;

class AclHttpErrors
{
    public const USER_LOGIN_NOT_SUPLIED = 1;
    public const PASSWORD_NOT_SUPLIED = 2;

    public const INVALID_CREDENTIALS = 11;
    public const INACTIVE_USER = 12;
    public const BLOCKED_USER = 13;

    public const NO_LOGGED_USER = 21;

    public const ERROR_MSG = [
        self::USER_LOGIN_NOT_SUPLIED => 'User login not supplied.',
        self::PASSWORD_NOT_SUPLIED => 'Password not supplied.',

        self::INVALID_CREDENTIALS => 'Invalid credentials.',
        self::INACTIVE_USER => 'Inactive user.',
        self::BLOCKED_USER => 'Blocked user.',

        self::NO_LOGGED_USER => 'No logged User.',
    ];

    public static function getErrorMsg($code, $default = null)
    {
        return array_key_exists($code, static::ERROR_MSG) ? static::ERROR_MSG[$code] : $default;
    }
}
