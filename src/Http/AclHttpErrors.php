<?php

namespace Antares\Acl\Http;

use Antares\Http\AbstractHttpErrors;

class AclHttpErrors extends AbstractHttpErrors
{
    public const UNAUTHENTICATED = 990001;

    public const USER_LOGIN_NOT_SUPLIED = 990011;
    public const PASSWORD_NOT_SUPLIED = 990012;

    public const INVALID_CREDENTIALS = 990021;
    public const INACTIVE_USER = 990022;
    public const BLOCKED_USER = 990023;

    public const NO_LOGGED_USER = 990031;

    public const MESSAGES = [
        self::UNAUTHENTICATED => 'Unauthenticated request',

        self::USER_LOGIN_NOT_SUPLIED => 'User login not supplied',
        self::PASSWORD_NOT_SUPLIED => 'Password not supplied',

        self::INVALID_CREDENTIALS => 'Invalid credentials',
        self::INACTIVE_USER => 'Inactive user',
        self::BLOCKED_USER => 'Blocked user',

        self::NO_LOGGED_USER => 'No logged User',
    ];
}
