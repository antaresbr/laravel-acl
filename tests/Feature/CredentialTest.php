<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Models\User;
use Antares\Acl\Tests\DatabaseTrait;
use Antares\Acl\Tests\TestCase;

class CredentialTest extends TestCase
{
    use DatabaseTrait;

    private function defineUserAttribute($id, $attribute, $value)
    {
        $user = User::find($id);
        $user->{$attribute} = $value;
        $user->save();
    }

    /** @test */
    public function login_with_missing_credentials()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $response = $this->post($route, []);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::USER_LOGIN_NOT_SUPLIED,
        ]);
    }

    /** @test */
    public function login_with_missing_password()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $response = $this->post($route, [
            'login' => 'wrong_user',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::PASSWORD_NOT_SUPLIED,
        ]);
    }

    /** @test */
    public function login_with_invalid_credentials()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $response = $this->post($route, [
            'login' => 'wrong_user',
            'password' => 'wrong_password',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::INVALID_CREDENTIALS,
        ]);
    }

    /** @test */
    public function login_with_inactive_user()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $this->defineUserAttribute(1, 'active', 0);
        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::INACTIVE_USER,
        ]);
        $this->defineUserAttribute(1, 'active', 1);
    }

    /** @test */
    public function login_with_blocked_user()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $this->defineUserAttribute(1, 'blocked', true);
        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'code' => AclHttpErrors::BLOCKED_USER,
        ]);
        $this->defineUserAttribute(1, 'blocked', false);
    }
}
