<?php

namespace Antares\Acl\Traits;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\User;
use Antares\Http\JsonResponse;
use Antares\Support\Str;
use Illuminate\Support\Facades\DB;

trait AclAuthorizeTrait
{
    use AclPathTrait;

    /**
     * Get user rights
     *
     * @param User $user
     * @param strning|array pathFilter
     * @return \Illuminate\Support\Collection
     */
    public function aclGetRights(User $user, $pathFilter = '')
    {
        if ($pathFilter) {
            $pathFilter = is_array($pathFilter) ? $pathFilter : $this->aclPathToFilter($pathFilter);
        }
        $userRights = DB::table('acl_user_rights');
        $userRights->join('acl_menus', 'acl_menus.id', '=', 'acl_user_rights.menu_id');
        $userRights->where([
            'acl_user_rights.user_id' => $user->id,
            'acl_user_rights.enabled' => true,
            'acl_menus.enabled' => true,
        ]);
        if ($pathFilter) {
            $userRights->whereIn('acl_menus.path', $pathFilter);
        }
        $userRights->select('acl_menus.path as path', 'acl_user_rights.right as right')->distinct();

        $allRights = DB::table('acl_group_rights');
        $allRights->join('acl_menus', 'acl_menus.id', '=', 'acl_group_rights.menu_id');
        $allRights->join('acl_groups', 'acl_groups.id', '=', 'acl_group_rights.group_id');
        $allRights->join('acl_user_groups', 'acl_user_groups.group_id', '=', 'acl_group_rights.group_id');
        $allRights->where([
            'acl_user_groups.user_id' => $user->id,
            'acl_group_rights.enabled' => true,
            'acl_groups.enabled' => true,
            'acl_menus.enabled' => true,
        ]);
        if ($pathFilter) {
            $allRights->whereIn('acl_menus.path', $pathFilter);
        }
        $allRights->select('acl_menus.path as path', 'acl_group_rights.right as right')->distinct();
        $allRights->union($userRights, false);
        $allRights->orderBy('path')->orderBy('right');

        $items = $allRights->get()->filter(function ($item) {
            return $this->aclIsEnabledPath($item->path);
        });

        return $items;
    }

    /**
     * Check if the supplied path is allowed for the user
     *
     * @param User $user
     * @param strning path
     * @return boolean
     */
    public function aclIsAllowedPath(User $user, $path)
    {
        $isAllowed = false;

        if ($path) {
            if (!$this->aclIsEnabledPath($path)) {
                return false;
            }

            $rights = $this->aclGetRights($user, $path);

            foreach ($rights as $right) {
                $isAllowed = boolval($right->right);
                if (!$isAllowed) {
                    return false;
                }
            }
        }

        return $isAllowed;
    }

    /**
     * Authorize action
     *
     * @param string $action
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function aclAuthorize($action = '')
    {
        $user = request()->user('acl');
        if (empty($user)) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::NO_AUTHENTICATED_USER))->setStatusCode(401);
        }

        $path = $this->menuPath();
        if (empty($path)) {
            return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::MENU_PATH_NOT_SUPPLIED), null, [
                'action' => $action,
            ])->setStatusCode(400);
        }
        $action = $this->aclTrimPath($action);
        $fullPath = empty($action) ? $path : Str::join('/', $path, $action);

        if (!Str::endsWith($fullPath, '/metadata')) {
            $menu = AclMenu::where('path', $fullPath)->get()->first();
            if (empty($menu)) {
                return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::MENU_PATH_NOT_FOUND), null, [
                    'path' => $fullPath,
                ])->setStatusCode(404);
            }

            if ($this->aclIsAllowedPath($user, $fullPath) !== true) {
                return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::MENU_PATH_ACCESS_NOT_ALLOWED), null, [
                    'path' => $fullPath,
                ])->setStatusCode(403);
            }
        }

        return true;
    }
}
