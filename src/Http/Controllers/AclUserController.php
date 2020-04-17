<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Http\AclHttpResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AclUserController extends Controller
{
    public function getLoggedUser(Request $request)
    {
        $user = $request->user();

        return empty($user)
            ? AclHttpResponse::error(AclHttpErrors::NO_LOGGED_USER)
            : AclHttpResponse::successful(['user' => $user]);
    }
}
