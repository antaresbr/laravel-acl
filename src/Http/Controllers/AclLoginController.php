<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AclLoginController extends Controller
{
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

        $session = (new AclSessionController())->getValidSession($user);

        return JsonResponse::successful([
            'api_token' => "{$session->id}.{$session->api_token}",
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            JsonResponse::error(AclHttpErrors::NO_AUTHENTICATED_USER);
        }

        return (new AclSessionController())->invalidateSessionFromRequest($request);
    }
}
