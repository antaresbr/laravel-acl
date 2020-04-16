<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Models\AclSession;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AclLoginController extends Controller
{
    protected function jsonError($msg, $code = -1)
    {
        return json_encode([
            'status' => 'error',
            'error_code' => $code,
            'error_msg' => $msg,
        ]);
    }

    protected function jsonSuccessful($token)
    {
        return json_encode([
            'status' => 'successful',
            'token' => $token,
        ]);
    }

    protected function getValidAclSession($user_id)
    {
        $session = AclSession::where([
            ['user_id', $user_id],
            ['valid', 1],
            ['finished_at', null],
            ['expires_at', '>=', Carbon::now()->addSeconds(config('acl.session.reuse_ttl'))->format(config('acl.date_format'))]
        ])->orderBy('issued_at', 'desc')->first();

        if (empty($session)) {
            $session = AclSession::create([
                'api_token' => 'temp.' . Str::random(32),
                'user_id' => $user_id,
            ])->refresh();
            $session->expires_at = Carbon::parse($session->issued_at)->addSeconds(config('acl.session.ttl'))->format($session->getDateFormat());

            $payload = [
                'iss' => config('acl.jwt.issuer'),
                'sub' => config('app.name'),
                'website' => config('app.url'),
                'sid' => $session->id,
                'user' => $user_id,
                'issued_at' => $session->issued_at,
                'expires_at' => $session->expires_at,
            ];

            $session->api_token = JWT::encode($payload, config('acl.jwt.key'), config('acl.jwt.alg'));
            $session->save();
        }

        return $session;
    }

    public function login(Request $request)
    {
        $guard = Auth::guard('acl');
        $provider = $guard->getProvider();
        $usernameField = $provider->createModel()->username();

        $credentials = $guard->getCredentialsForRequest();

        if (empty($credentials[$usernameField])) {
            return $this->jsonError(__('User login not supplied.'), AclLoginErrors::USER_LOGIN_NOT_SUPLIED);
        }
        if (empty($credentials['password'])) {
            return $this->jsonError(__('Password not supplied.'), AclLoginErrors::PASSWORD_NOT_SUPLIED);
        }

        $candidate = $provider->retrieveByCredentials($credentials);
        $user = (!empty($candidate) and $provider->validateCredentials($candidate, $credentials)) ? $candidate : null;

        if (empty($user)) {
            return $this->jsonError(__('Invalid credentials.'), AclLoginErrors::INVALID_CREDENTIALS);
        }
        if (!$user->active) {
            return $this->jsonError(__('Inactive user.'), AclLoginErrors::INACTIVE_USER);
        }
        if ($user->blocked) {
            return $this->jsonError(__('Blocked user.'), AclLoginErrors::BLOCKED_USER);
        }

        $session = $this->getValidAclSession($user->id);

        return json_encode([
            'status' => 'successful',
            'api_token' => "{$session->id}.{$session->api_token}"
        ]);
    }
}
