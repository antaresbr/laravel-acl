<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Http\Controllers\AclSessionController;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Tests\Models\User;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\AuthenticateUserTrait;
use Antares\Acl\Tests\Traits\ResetDatabaseTrait;
use Illuminate\Support\Carbon;

class AuthenticationsTest extends TestCase
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
    public function invalidate_all_sessions()
    {
        AclSession::where('valid', true)->update(['valid' => false]);
        $this->assertCount(0, AclSession::where('valid', true)->get());
    }

    /** @test */
    public function login_with_random_user()
    {
        return $this->loginRandomUser();
    }

    /**
     * @test
     * @depends login_with_random_user
     */
    public function get_logged_data($auth)
    {
        return $this->getLoggedData($auth);
    }

    /**
     * @test
     * @depends get_logged_data
     */
    public function login_with_same_user($data)
    {
        $this->assertArrayHasKey('api_token', $data);
        $this->assertArrayHasKey('logged_user', $data);

        $user = User::findOrFail($data['logged_user']['id']);
        $json = $this->loginUser($user->email, 'secret');

        $this->assertEquals($data['api_token'], $json['data']['api_token']);

        $sessions = AclSession::where(['user_id' => $user->id, 'valid' => true])->get();
        $this->assertCount(1, $sessions);

        $session = $sessions[0];
        $this->assertEquals($data['api_token'], "{$session->id}.{$session->api_token}");

        return $data;
    }

    /**
     * @test
     * @depends get_logged_data
     */
    public function validate_session($data)
    {
        $sessionController = new AclSessionController();

        $session = $sessionController->sessionFromToken($data['api_token']);
        $this->assertInstanceOf(AclSession::class, $session);
        $this->assertTrue($sessionController->isValidSession($session));

        $sessionData = $session->toArray();

        $session->valid = false;
        $session->save();
        $this->assertFalse($sessionController->isValidSession($session));
        $session->valid = $sessionData['valid'];
        $session->save();
        $this->assertTrue($sessionController->isValidSession($session));

        $session->expires_at = Carbon::now()->subDays(1);
        $session->save();
        $this->assertFalse($sessionController->isValidSession($session));
        $session->expires_at = $sessionData['expires_at'];
        $session->save();
        $this->assertTrue($sessionController->isValidSession($session));

        $user = User::findOrFail($data['logged_user']['id']);
        $this->assertEquals($user->id, $session->user_id);

        $user->inactive();
        $this->assertFalse($sessionController->isValidSession($session));
        $user->enable();
        $user->block();
        $this->assertFalse($sessionController->isValidSession($session));
        $user->enable();

        return $data;
    }

    /**
     * @test
     * @depends validate_session
     */
    public function invalidate_current_session($data)
    {
        $this->assertArrayHasKey('api_token', $data);
        $this->assertArrayHasKey('logged_user', $data);

        $sessionController = new AclSessionController();

        $session = $sessionController->sessionFromToken($data['api_token']);
        $this->assertInstanceOf(AclSession::class, $session);
        $this->assertEquals($data['api_token'], "{$session->id}.{$session->api_token}");

        $invalidSession = $sessionController->invalidateSession($session);
        $this->assertEquals($session->id, $invalidSession->id);
        $this->assertFalse($session->valid);
    }

    /** @test */
    public function create_multiple_sections_for_random_user()
    {
        $this->assertCount(0, AclSession::where('valid', true)->get());

        $user = User::all()->random(1)->first();
        $this->assertInstanceOf(User::class, $user);

        $amount = rand(3, 10);
        $sessions = AclSession::factory()->count($amount)->create(['user_id' => $user->id, 'valid' => true]);
        $this->assertCount($amount, $sessions);
        $this->assertInstanceOf(AclSession::class, $sessions[rand(0, $amount - 1)]);

        return [
            'user' => $user,
            'amount' => $amount,
        ];
    }

    /**
     * @test
     * @depends create_multiple_sections_for_random_user
     */
    public function invalidate_sessions_from_user($data)
    {
        $filters = [
            'user_id' => $data['user']->id,
            'valid' => true,
        ];
        $this->assertCount($data['amount'], AclSession::where($filters)->get());
        (new AclSessionController())->invalidateSessionsFromUser($data['user']);
        $this->assertCount(0, AclSession::where($filters)->get());
    }

    /** @test */
    public function login_with_other_user()
    {
        return $this->loginRandomUser();
    }

    /**
     * @test
     * @depends login_with_other_user
     */
    public function user_logout($auth)
    {
        $this->logoutUser($auth);
    }
}
