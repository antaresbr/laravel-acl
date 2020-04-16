<?php

namespace Antares\Acl\Http\Controllers;

class AclLoginErrors
{
    public const USER_LOGIN_NOT_SUPLIED = 1;
    public const PASSWORD_NOT_SUPLIED = 2;

    public const INVALID_CREDENTIALS = 101;
    public const INACTIVE_USER = 102;
    public const BLOCKED_USER = 103;
}
