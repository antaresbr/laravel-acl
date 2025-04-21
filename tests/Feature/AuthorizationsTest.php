<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Http\Controllers\AclAuthorizeController;
use Antares\Acl\Tests\Models\User;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\AuthenticateUserTrait;
use Antares\Acl\Tests\Traits\ResetDatabaseTrait;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

class AuthorizationsTest extends TestCase
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

    private function getAuthorizeRoute()
    {
        return config('acl.route.prefix.api') . '/authorize';
    }

    private function authorizePathRequest($auth, $path, $action = '', $status = 'successful', $errorCode = null, $httpStatus = 200)
    {
        $token = $auth['data']['api_token'] ?? $auth['api_token'] ?? $auth;
        $response = $this->post($this->getAuthorizeRoute(), [
            'path' => $path,
            'action' => $action,
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ]);
        $response->assertStatus($httpStatus);

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
    public function is_enabled_path()
    {
        $authController = new AclAuthorizeController();

        $this->assertTrue($authController->aclIsEnabledPath('root/menu-01'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-01/path-01'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-01/path-01/option-01'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-01/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-01/path-01/option-01/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-01/path-01/option-02/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-01/path-02/option-03/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-02/path-03/option-03/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-02/path-02/option-03/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-02/path-02/option-02/action-03'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-02/path-02/option-02/action-02'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-03/path-02/option-03/action-03'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-03/path-03/option-03/action-03'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-04/path-02/option-03/action-03'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-04/path-03/option-03/action-03'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsEnabledPath('root/menu-05/path-02/option-03/action-03'));
        $this->assertTrue($authController->aclIsEnabledPath('root/menu-05/path-03/option-03/action-03'));
    }

    #[Test]
    public function user_21_rights()
    {
        $authController = new AclAuthorizeController();
        $user = User::findOrFail(21)->enable();

        $rights = $authController->aclGetRights($user);
        $this->assertCount(6, $rights);

        $rights = $authController->aclGetRights($user, 'root/menu-01');
        $this->assertCount(1, $rights);
    }

    #[Test]
    public function login_user_21()
    {
        $user = User::findOrFail(21)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_21')]
    public function authorize_user_21($auth)
    {
        $user = $this->getLoggedUser($auth);

        $authController = new AclAuthorizeController();

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05/path-03/option-03/action-03'));
    }

    #[Test]
    public function login_user_22()
    {
        $user = User::findOrFail(22)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_22')]
    public function authorize_user_22($auth)
    {
        $user = $this->getLoggedUser($auth);

        $authController = new AclAuthorizeController();

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-01/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-03/option-03/action-03'));
    }

    #[Test]
    public function login_user_23()
    {
        $user = User::findOrFail(23)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_23')]
    public function authorize_user_23($auth)
    {
        $user = $this->getLoggedUser($auth);

        $authController = new AclAuthorizeController();

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-03/option-03/action-03'));
    }

    #[Test]
    public function login_user_24()
    {
        $user = User::findOrFail(24)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_24')]
    public function authorize_user_24($auth)
    {
        $user = $this->getLoggedUser($auth);

        $authController = new AclAuthorizeController();

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-03/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-03/option-03/action-03'));
    }

    #[Test]
    public function login_user_25()
    {
        $user = User::findOrFail(25)->enable();
        return $this->loginUser($user->email, 'secret');
    }

    #[Test]
    #[Depends('login_user_25')]
    public function authorize_user_25($auth)
    {
        $user = $this->getLoggedUser($auth);

        $authController = new AclAuthorizeController();

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-01/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-02/path-03/option-03/action-03'));

        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-01/option-01/action-02'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-03/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-04/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-04/path-03/option-03/action-03'));

        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-01'));
        $this->assertFalse($authController->aclIsAllowedPath($user, 'root/menu-05/path-01/option-01/action-02'));
        $this->assertTrue($authController->aclIsAllowedPath($user, 'root/menu-05/path-03/option-03/action-03'));

        return $auth;
    }

    #[Test]
    #[Depends('login_user_25')]
    public function authorize_request($auth)
    {
        $this->authorizePathRequest($auth, '', '', 'error', AclHttpErrors::MENU_PATH_NOT_SUPPLIED, 404);
        $this->authorizePathRequest($auth, 'root/orange', '', 'error', AclHttpErrors::MENU_PATH_NOT_FOUND, 404);
        $this->authorizePathRequest($auth, 'root/menu-02', '', 'error', AclHttpErrors::MENU_PATH_ACCESS_NOT_ALLOWED, 403);
        $this->authorizePathRequest($auth, 'root/menu-04', 'path-02', 'error', AclHttpErrors::MENU_PATH_ACCESS_NOT_ALLOWED, 403);
        $this->authorizePathRequest($auth, 'root/menu-04', 'path-01', 'successful');
    }
}
