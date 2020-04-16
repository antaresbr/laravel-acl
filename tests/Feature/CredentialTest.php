<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\Controllers\AclLoginErrors;
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
            'error_code' => AclLoginErrors::USER_LOGIN_NOT_SUPLIED,
        ]);

        $response = $this->post($route, [
            'login' => 'wrong_user',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'error_code' => AclLoginErrors::PASSWORD_NOT_SUPLIED,
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
            'error_code' => AclLoginErrors::INVALID_CREDENTIALS,
        ]);
    }

    /** @test */
    public function login_with_inactive_or_blocked_user()
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
            'error_code' => AclLoginErrors::INACTIVE_USER,
        ]);

        $this->defineUserAttribute(1, 'active', 1);
        $this->defineUserAttribute(1, 'blocked', 1);
        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'error_code' => AclLoginErrors::BLOCKED_USER,
        ]);
        $this->defineUserAttribute(1, 'active', 1);
    }
}
