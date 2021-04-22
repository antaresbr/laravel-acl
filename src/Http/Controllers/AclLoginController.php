<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Models\AclSession;
use Antares\Http\JsonResponse;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AclLoginController extends Controller
{
    protected function getValidAclSession($user_id)
    {
        $session = AclSession::where([
            ['user_id', $user_id],
            ['valid', 1],
            ['finished_at', null],
            ['expires_at', '>=', Carbon::now()->addSeconds(config('acl.session.reuse_ttl'))->format(config('acl.date_format'))],
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
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::USER_LOGIN_NOT_SUPPLIED));
        }
        if (empty($credentials['password'])) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::PASSWORD_NOT_SUPPLIED));
        }

        $candidate = $provider->retrieveByCredentials($credentials);
        $user = (!empty($candidate) and $provider->validateCredentials($candidate, $credentials)) ? $candidate : null;

        if (empty($user)) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::INVALID_CREDENTIALS));
        }
        if (!$user->active) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::INACTIVE_USER));
        }
        if ($user->blocked) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::BLOCKED_USER));
        }

        $session = $this->getValidAclSession($user->id);

        return JsonResponse::successful([
            'api_token' => "{$session->id}.{$session->api_token}",
        ]);
    }
}
