<?php

namespace Antares\Acl\Traits;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Http\JsonResponse;
use Antares\Support\Str;

trait AclAuthorizeTrait
{
    public function menuPath()
    {
        return $this->menuPath ?? null;
    }

    /**
     * Authorize action
     *
     * @param string $action
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function aclAuthorize($action = '')
    {
        if (empty($this->menuPath())) {
            return JsonResponse::error(AclHttpErrors::MENUPATH_NOT_SUPPLIED, null, [
                'action' => $action,
            ]);
        }
        $action = Str::join('/', $this->menuId(), $action);

        //-- TODO : implement authorize function in laravel-acl
        // return Acl::authorize($this->menuPath, $action);

        return true;
    }
}
