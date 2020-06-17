<?php

namespace Antares\Acl\Http;

use Antares\Http\HttpErrors;

class AclHttpErrors extends HttpErrors
{
    public const UNAUTHENTICATED = 990001;

    public const USER_LOGIN_NOT_SUPLIED = 990011;
    public const PASSWORD_NOT_SUPLIED = 990012;

    public const INVALID_CREDENTIALS = 990021;
    public const INACTIVE_USER = 990022;
    public const BLOCKED_USER = 990023;

    public const NO_LOGGED_USER = 990031;

    protected function makeMessages()
    {
        parent::makeMessages();

        //-- register current class error messages
        if (!in_array(static::class, $this->registeredClasses)) {
            $this->registerClass(static::class);

            $this->addMessage(self::UNAUTHENTICATED, 'Unauthenticated request.');

            $this->addMessage(self::USER_LOGIN_NOT_SUPLIED, 'User login not supplied.');
            $this->addMessage(self::PASSWORD_NOT_SUPLIED, 'Password not supplied.');

            $this->addMessage(self::INVALID_CREDENTIALS, 'Invalid credentials.');
            $this->addMessage(self::INACTIVE_USER, 'Inactive user.');
            $this->addMessage(self::BLOCKED_USER, 'Blocked user.');

            $this->addMessage(self::NO_LOGGED_USER, 'No logged User.');
        }
    }
}
