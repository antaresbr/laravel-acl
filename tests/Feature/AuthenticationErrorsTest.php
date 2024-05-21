<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\AuthenticateUserTrait;
use Antares\Acl\Tests\Traits\ResetDatabaseTrait;

class AuthenticationErrorsTest extends TestCase
{
    use AuthenticateUserTrait;
    use ResetDatabaseTrait;

    /** @test */
    public function reset_database()
    {
        $this->resetDatabase();
    }

    /** @test */
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    /** @test */
    public function database_seed()
    {
        $this->seedDatabase();
    }

    /** @test */
    public function login_with_missing_credentials()
    {
        $response = $this->post($this->getLoginRoute(), []);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::USER_LOGIN_NOT_SUPPLIED,
            'message' => __(AclHttpErrors::message(AclHttpErrors::USER_LOGIN_NOT_SUPPLIED)),
        ]);
    }

    /** @test */
    public function login_with_missing_password()
    {
        $response = $this->post($this->getLoginRoute(), [
            'login' => 'wrong_user',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::PASSWORD_NOT_SUPPLIED,
            'message' => __(AclHttpErrors::message(AclHttpErrors::PASSWORD_NOT_SUPPLIED)),
        ]);
    }

    /** @test */
    public function login_with_invalid_credentials()
    {
        $response = $this->post($this->getLoginRoute(), [
            'login' => 'wrong_user',
            'password' => 'wrong_password',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::INVALID_CREDENTIALS,
            'message' => __(AclHttpErrors::message(AclHttpErrors::INVALID_CREDENTIALS)),
        ]);
    }

    /** @test */
    public function login_with_inactive_user()
    {
        $user = $this->randomUser()->unblock()->inactive();
        $response = $this->post($this->getLoginRoute(), [
            'login' => $user->email,
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::INACTIVE_USER,
            'message' => __(AclHttpErrors::message(AclHttpErrors::INACTIVE_USER)),
        ]);
        $user->active();
    }

    /** @test */
    public function login_with_blocked_user()
    {
        $user = $this->randomUser()->active()->block();
        $response = $this->post($this->getLoginRoute(), [
            'login' => $user->username,
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::BLOCKED_USER,
            'message' => __(AclHttpErrors::message(AclHttpErrors::BLOCKED_USER)),
        ]);
    }
}
