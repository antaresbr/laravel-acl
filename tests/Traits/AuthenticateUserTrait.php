<?php

namespace Antares\Acl\Tests\Traits;

use Antares\Acl\Tests\Models\User;
use Antares\Http\JsonResponse;

trait AuthenticateUserTrait
{
    private function randomUser()
    {
        return User::all()->random();
    }

    private function getLoginRoute()
    {
        return config('acl.route.prefix.api') . '/login';
    }

    private function getLogoutRoute()
    {
        return config('acl.route.prefix.api') . '/logout';
    }

    private function getLoggedUserRoute()
    {
        return config('acl.route.prefix.api') . '/logged-user';
    }

    private function loginUser($login, $password)
    {
        $response = $this->post($this->getLoginRoute(), [
            'login' => $login,
            'password' => $password,
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => JsonResponse::SUCCESSFUL,
        ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('api_token', $json['data']);

        $token = explode('.', $json['data']['api_token']);
        $this->assertCount(4, $token);

        return $json;
    }

    private function loginRandomUser()
    {
        $user = $this->randomUser()->enable();
        return $this->loginUser($user->username, 'secret');
    }

    private function getLoggedData($auth)
    {
        $token = $auth['data']['api_token'] ?? $auth['api_token'] ?? $auth;
        $response = $this->get($this->getLoggedUserRoute(), [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('user', $json['data']);

        return [
            'api_token' => $token,
            'logged_user' => $json['data']['user'],
        ];
    }

    private function getLoggedUser($auth)
    {
        $data = $this->getLoggedData($auth);
        return User::findOrFail($data['logged_user']['id']);
    }

    private function logoutUser($auth)
    {
        $token = $auth['data']['api_token'] ?? $auth['api_token'] ?? $auth;
        $response = $this->get($this->getLogoutRoute(), [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('api_token', $json['data']);

        return $json['data'];
    }
}
