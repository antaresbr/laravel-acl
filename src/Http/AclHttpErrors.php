<?php

namespace Antares\Acl\Http;

use Antares\Http\AbstractHttpErrors;

class AclHttpErrors extends AbstractHttpErrors
{
    public const UNAUTHENTICATED = 990001;

    public const USER_LOGIN_NOT_SUPPLIED = 990011;
    public const PASSWORD_NOT_SUPPLIED = 990012;

    public const INVALID_CREDENTIALS = 990021;
    public const INACTIVE_USER = 990022;
    public const BLOCKED_USER = 990023;

    public const NO_AUTHENTICATED_USER = 990031;
    public const NO_SESSION_FOR_REQUEST = 990032;

    public const MENU_PATH_NOT_SUPPLIED = 990041;
    public const MENU_PATH_NOT_FOUND = 990042;
    public const MENU_PATH_ACCESS_NOT_ALLOWED = 990043;

    public const MESSAGES = [
        self::UNAUTHENTICATED => 'acl::errors.unauthenticated',

        self::USER_LOGIN_NOT_SUPPLIED => 'acl::errors.user_login_not_supplied',
        self::PASSWORD_NOT_SUPPLIED => 'acl::errors.password_not_supplied',

        self::INVALID_CREDENTIALS => 'acl::errors.invalid_credentials',
        self::INACTIVE_USER => 'acl::errors.inactive_user',
        self::BLOCKED_USER => 'acl::errors.blocked_user',

        self::NO_AUTHENTICATED_USER => 'acl::errors.no_authenticated_user',
        self::NO_SESSION_FOR_REQUEST => 'acl::errors.no_session_for_request',

        self::MENU_PATH_NOT_SUPPLIED => 'acl::errors.menu_path_not_supplied',
        self::MENU_PATH_NOT_FOUND => 'acl::errors.menu_path_not_found',
        self::MENU_PATH_ACCESS_NOT_ALLOWED => 'acl::errors.menu_path_access_not_allowed',
    ];
}
