<?php

namespace Antares\Acl\Services;

use Antares\Acl\Models\AclSession;
use Illuminate\Auth\TokenGuard;
use Illuminate\Support\Carbon;

class AclGuard extends TokenGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $session = $this->getAclSession();
        if (empty($session)) {
            return null;
        }

        $user = $this->provider->retrieveById($session->user_id);

        if (empty($user) or !$user->active or $user->blocked) {
            return null;
        }

        return $this->user = $user;
    }

    /**
     * Get a valid session object for the request
     *
     * @return Antares\Acl\Models\AclSession|null
     */
    public function getAclSession()
    {
        $token_pieces = explode('.', $this->getTokenForRequest() ?? '');
        if (count($token_pieces) != 4) {
            return null;
        }

        $session_id = array_shift($token_pieces);
        $token = implode('.', $token_pieces);

        return AclSession::where([
            ['id', $session_id],
            ['api_token', $token],
            ['valid', 1],
            ['finished_at', null],
            ['expires_at', '>=', Carbon::now()->format(config('acl.date_format'))]
        ])->first();
    }

    /**
     * Get the user credentials for the the request
     *
     * @return string
     */
    public function getCredentialsForRequest()
    {
        $usernameField = $this->provider->createModel()->username();

        return [
            $usernameField => request($usernameField),
            'password' => request('password'),
        ];
    }
}
