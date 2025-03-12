<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\User;
use Antares\Http\JsonResponse;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AclSessionController extends Controller
{
    /**
     * Check if a session is valid
     *
     * @param \Antares\Acl\Models\AclSession $session
     * @return boolean
     */
    public function isValidSession(AclSession $session)
    {
        if (empty($session)) {
            return false;
        }
        if (empty($session->id) or $session->id < 0) {
            return false;
        }
        if (!$session->valid or $session->expires_at < Carbon::now()->format(config('acl.date_format'))) {
            return false;
        }

        $user = User::find($session->user_id);
        if (empty($user)) {
            return false;
        }
        if (!$user->active or $user->blocked) {
            return false;
        }

        return true;
    }

    /**
     * Get valid session for a user
     *
     * @param \Antares\Acl\Models\User $user
     * @return \Antares\Acl\Models\AclSession
     */
    public function getValidSession(User $user)
    {
        $session = AclSession::where([
            ['user_id', $user->id],
            ['valid', 1],
            ['finished_at', null],
            ['expires_at', '>=', Carbon::now()->addSeconds(config('acl.session.reuse_ttl'))->format(config('acl.date_format'))],
        ])->orderBy('issued_at', 'desc')->first();

        if (empty($session)) {
            $session = AclSession::create([
                'api_token' => 'temp.' . Str::random(32),
                'user_id' => $user->id,
            ])->refresh();
            $session->expires_at = Carbon::parse($session->issued_at)->addSeconds(config('acl.session.ttl'))->format($session->getDateFormat());

            $payload = [
                'iss' => config('acl.jwt.issuer'),
                'sub' => config('app.name'),
                'website' => config('app.url'),
                'sid' => $session->id,
                'user' => $user->id,
                'issued_at' => $session->issued_at,
                'expires_at' => $session->expires_at,
            ];

            $session->api_token = JWT::encode($payload, config('acl.jwt.key'), config('acl.jwt.alg'));
            $session->save();
        }

        return $session;
    }

    /**
     * Return the session model from given token
     *
     * @param \Illuminate\Http\Request $request
     * @return null|\Antares\Acl\Models\AclSession
     */
    public function sessionFromToken($token)
    {
        $session = null;

        $pieces = is_array($token) ? $token : explode('.', $token);
        if (count($pieces) == 4) {
            $id = array_shift($pieces);
            $session = AclSession::where(['id' => $id, 'api_token' => implode('.', $pieces)])->first();
        }

        return $session;
    }

    /**
     * Return the session model from current request
     *
     * @param \Illuminate\Http\Request $request
     * @return null|\Antares\Acl\Models\AclSession
     */
    public function sessionFromRequest(Request $request)
    {
        return $this->sessionFromToken($request->bearerToken());
    }

    /**
     * Return JsonResponse with the session model from current request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessionFromRequest(Request $request)
    {
        $session = $this->sessionFromToken($request->bearerToken());

        return empty($session)
            ? JsonResponse::error(AclHttpErrors::NO_SESSION_FOR_REQUEST)
            : JsonResponse::successful(['session' => $session]);
    }

    /**
     * Invalidate the session model
     *
     * @param \Antares\Acl\Models\AclSession $session
     * @return \Antares\Acl\Models\AclSession
     */
    public function invalidateSession(AclSession $session)
    {
        $delta = ['valid' => false];
        if (!$session->finished_at) {
            $delta['finished_at'] = Carbon::now()->format(config('acl.date_format'));
        }
        $session->update($delta);
        $session->save();

        return $session;
    }

    /**
     * Invalidate the session model from request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Antares\Http\JsonResponse
     */
    public function invalidateSessionFromRequest(Request $request)
    {
        $session = $this->sessionFromRequest($request);
        if (empty($session)) {
            return JsonResponse::error(AclHttpErrors::NO_SESSION_FOR_REQUEST);
        }

        $session = $this->invalidateSession($session);

        return JsonResponse::successful(['api_token' => $request->bearerToken()]);
    }

    /**
     * Invalidate sessions model from user
     *
     * @param \Antares\Acl\Models\User $user
     * @return array
     */
    public function invalidateSessionsFromUser(User $user)
    {
        $items = [];

        $sessions = AclSession::where([
            ['user_id', $user->id],
            ['valid', '=', true],
        ])->get();

        foreach ($sessions as $session) {
            $this->invalidateSession($session);
            $items[$session->id] = $session->api_token;
        }

        return $items;
    }

    /**
     * Return the session model from current request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Antares\Http\JsonResponse
     */
    public function invalidateExpiredSessions(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            return JsonResponse::error(AclHttpErrors::NO_AUTHENTICATED_USER);
        }

        $items = [];

        $sessions = AclSession::where([
            ['user_id', $user->id],
            ['valid', '=', true],
            ['expires_at', '<=', Carbon::now()->format(config('acl.date_format'))],
        ])->get();

        foreach ($sessions as $session) {
            $this->invalidateSession($session);
            $items[$session->id] = $session->api_token;
        }

        return JsonResponse::successful(['items' => $items]);
    }
}
