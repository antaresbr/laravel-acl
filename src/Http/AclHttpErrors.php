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

    public const NO_AUTHENTICATED_USER = 990031;

    public const MESSAGES = [
        self::UNAUTHENTICATED => 'acl::errors.unauthenticated',

        self::USER_LOGIN_NOT_SUPLIED => 'acl::errors.user_login_not_supplied',
        self::PASSWORD_NOT_SUPLIED => 'acl::errors.password_not_supplied',

        self::INVALID_CREDENTIALS => 'acl::errors.invalid_credentials',
        self::INACTIVE_USER => 'acl::errors.inactive_user',
        self::BLOCKED_USER => 'acl::errors.blocked_user',

        self::NO_AUTHENTICATED_USER => 'acl::errors.no_authenticated_user',
    ];
}
