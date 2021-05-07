<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Traits\AclAuthorizeTrait;
use Antares\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AclAuthorizeController extends Controller
{
    use AclAuthorizeTrait;

    /**
     * Authorize a user against a path
     *
     * @param \Illuminate\Http\Request $request
     * @return \Antares\Http\JsonResponse
     */
    public function authorize(Request $request)
    {
        $this->menuPath = $request->input('path');
        $action = $request->input('action');

        $r = $this->aclAuthorize($action);
        if ($r !== true) {
            return $r;
        }

        return JsonResponse::successful([
            'path' => $this->menuPath(),
            'action' => $action,
            'allowed' => true,
        ]);
    }
}
