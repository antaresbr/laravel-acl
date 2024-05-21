<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AclUserController extends Controller
{
    public function getLoggedUser(Request $request)
    {
        $user = $request->user();

        return empty($user)
            ? JsonResponse::error(AclHttpErrors::error(AclHttpErrors::NO_AUTHENTICATED_USER))
            : JsonResponse::successful(['user' => $user]);
    }
}
