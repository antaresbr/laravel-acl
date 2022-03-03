<?php

namespace Antares\Acl\Http\Controllers;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Traits\AclMenuTrait;
use Antares\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AclMenuController extends Controller
{
    use AclMenuTrait;

    /**
     * Get menu tree for logged user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenuTree(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::NO_AUTHENTICATED_USER));
        }

        $path = $request->input('path');
        if ($path) {
            $r = $this->aclPathExists($path);
            if ($r !== true) {
                return $r;
            }
        }

        $data = $this->aclGetMenuTree($user, $path);

        return JsonResponse::successful($data->toArray());
    }

    /**
     * Get menu rights
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenuRights(Request $request)
    {
        $user = $request->user();
        if (empty($user)) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::NO_AUTHENTICATED_USER));
        }

        $path = $request->input('path');
        if ($path) {
            $r = $this->aclPathExists($path);
            if ($r !== true) {
                return $r;
            }
        }

        $data = $this->aclGetRights($user, $path);

        return JsonResponse::successful($data->toArray());
    }
}
