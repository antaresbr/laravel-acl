<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\AclHttpResponse;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Tests\DatabaseTrait;
use Antares\Acl\Tests\TestCase;

class LoginAdminUserTest extends TestCase
{
    use DatabaseTrait;

    public function loginAdminUser()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => AclHttpResponse::SUCCESSFUL,
        ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('api_token', $json['data']);

        $token = explode('.', $json['data']['api_token']);
        $this->assertCount(4, $token);

        return $json;
    }

    /** @test */
    public function first_login_with_admin_user()
    {
        return $this->loginAdminUser();
    }

    /**
     * @test
     * @depends first_login_with_admin_user
     */
    public function second_login_with_admin_user($auth)
    {
        $json = $this->loginAdminUser();

        $this->assertEquals($auth['data']['api_token'], $json['data']['api_token']);

        $sessions = AclSession::all();
        $this->assertCount(1, $sessions);

        $session = $sessions[0];
        $this->assertEquals($auth['data']['api_token'], "{$session->id}.{$session->api_token}");

        return $json;
    }

    /**
     * @test
     * @depends second_login_with_admin_user
     */
    public function get_logged_user($auth)
    {
        $route = config('acl.route.prefix.api') . '/logged-user';

        $response = $this->get($route, [
            'Authorization' => "Bearer {$auth['data']['api_token']}",
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('user', $json['data']);
    }
}
