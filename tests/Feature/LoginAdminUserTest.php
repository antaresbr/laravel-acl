<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Models\AclSession;
use Antares\Acl\Tests\DatabaseTrait;
use Antares\Acl\Tests\TestCase;

class LoginAdminUserTest extends TestCase
{
    use DatabaseTrait;

    /** @test */
    public function login_admin_user()
    {
        $route = config('acl.route.prefix.api') . '/login';

        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'successful',
        ]);

        $json1 = $response->json();
        $this->assertArrayHasKey('api_token', $json1);

        $response = $this->post($route, [
            'login' => 'admin@admin.org',
            'password' => 'secret',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'successful',
        ]);

        $json2 = $response->json();
        $this->assertArrayHasKey('api_token', $json1);

        $this->assertEquals($json1['api_token'], $json2['api_token']);

        $sessions = AclSession::all();
        $this->assertCount(1, $sessions);

        $session = $sessions[0];
        $this->assertEquals($json1['api_token'], "{$session->id}.{$session->api_token}");
    }
}
