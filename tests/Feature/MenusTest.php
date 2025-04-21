<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Tests\Models\User;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\AuthenticateUserTrait;
use Antares\Acl\Tests\Traits\ResetDatabaseTrait;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

class MenusTest extends TestCase
{
    use AuthenticateUserTrait;
    use ResetDatabaseTrait;

    #[Test]
    public function reset_database()
    {
        $this->resetDatabase();
    }

    #[Test]
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    #[Test]
    public function database_seed()
    {
        $this->seedDatabase();
    }

    private function getMenuTreeRoute()
    {
        return config('acl.route.prefix.api') . '/get-menu-tree';
    }

    private function getMenuTreeRequest($auth, $path = '', $status = 'successful', $errorCode = null)
    {
        $token = $auth['data']['api_token'] ?? $auth['api_token'] ?? $auth;
        $response = $this->post($this->getMenuTreeRoute(), [
            'path' => $path,
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals($status, $json['status']);

        if ($errorCode) {
            $this->assertEquals($errorCode, $json['code']);
        }

        return $json;
    }

    #[Test]
    public function login_user_21()
    {
        $user = User::findOrFail(21)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_21')]
    public function get_menu_for_user_21($auth)
    {
        $json = $this->getMenuTreeRequest($auth, 'root/menu-99', 'error', AclHttpErrors::MENU_PATH_NOT_FOUND);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-03');
        $this->assertIsArray($json['data']);
        $this->assertCount(0, $json['data']);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-04');
        $this->assertIsArray($json['data']);
        $this->assertCount(6, $json['data']);

        $json = $this->getMenuTreeRequest($auth);
        $this->assertIsArray($json['data']);
        $this->assertCount(36, $json['data']);
    }

    #[Test]
    public function login_user_22()
    {
        $user = User::findOrFail(22)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_22')]
    public function get_menu_for_user_22($auth)
    {
        $json = $this->getMenuTreeRequest($auth, 'root/menu-04');
        $this->assertIsArray($json['data']);
        $this->assertCount(0, $json['data']);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-01');
        $this->assertIsArray($json['data']);
        $this->assertCount(16, $json['data']);

        $json = $this->getMenuTreeRequest($auth);
        $this->assertIsArray($json['data']);
        $this->assertCount(31, $json['data']);
    }

    #[Test]
    public function login_user_23()
    {
        $user = User::findOrFail(23)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_23')]
    public function get_menu_for_user_23($auth)
    {
        $json = $this->getMenuTreeRequest($auth, 'root/menu-01');
        $this->assertIsArray($json['data']);
        $this->assertCount(0, $json['data']);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-03');
        $this->assertIsArray($json['data']);
        $this->assertCount(16, $json['data']);

        $json = $this->getMenuTreeRequest($auth);
        $this->assertIsArray($json['data']);
        $this->assertCount(16, $json['data']);
    }

    #[Test]
    public function login_user_24()
    {
        $user = User::findOrFail(24)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_24')]
    public function get_menu_for_user_24($auth)
    {
        $json = $this->getMenuTreeRequest($auth, 'root/menu-01');
        $this->assertIsArray($json['data']);
        $this->assertCount(0, $json['data']);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-03');
        $this->assertIsArray($json['data']);
        $this->assertCount(16, $json['data']);

        $json = $this->getMenuTreeRequest($auth);
        $this->assertIsArray($json['data']);
        $this->assertCount(31, $json['data']);
    }

    #[Test]
    public function login_user_25()
    {
        $user = User::findOrFail(25)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_25')]
    public function get_menu_for_user_25($auth)
    {
        $json = $this->getMenuTreeRequest($auth, 'root/menu-01');
        $this->assertIsArray($json['data']);
        $this->assertCount(0, $json['data']);

        $json = $this->getMenuTreeRequest($auth, 'root/menu-04');
        $this->assertIsArray($json['data']);
        $this->assertCount(16, $json['data']);

        $json = $this->getMenuTreeRequest($auth);
        $this->assertIsArray($json['data']);
        $this->assertCount(31, $json['data']);
    }
}
