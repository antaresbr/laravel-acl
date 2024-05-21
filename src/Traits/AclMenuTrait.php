<?php

namespace Antares\Acl\Traits;

use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\User;
use Antares\Foundation\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait AclMenuTrait
{
    use AclAuthorizeTrait;

    /**
     * Get user menu tree
     *
     * @param User $user
     * @param strning|array path
     * @return \Illuminate\Support\Collection
     */
    public function aclGetMenuTree(User $user, $path = '')
    {
        $data = new Collection();

        if ($user) {
            $path = $this->aclTrimPath($path);
            $rights = $this->aclGetRights($user)->filter(function ($item) use ($path) {
                return empty($path) ? true : Str::startsWith($item->path . '/', $path . '/');
            });

            if ($rights->isNotEmpty()) {
                $allowed = $rights->filter(function ($item) {
                    return ($item->right == true);
                });
                if ($allowed->isNotEmpty()) {
                    $menus = DB::table('acl_menus')->where(function ($query) use ($allowed) {
                        foreach ($allowed as $item) {
                            $query->orWhere('path', $item->path);
                            $query->orWhere('path', 'like', $item->path . '/%');
                        }
                    });

                    $denied = $rights->filter(function ($item) {
                        return ($item->right != true);
                    });
                    if ($denied->isNotEmpty()) {
                        foreach ($denied as $item) {
                            $menus->where('path', '!=', $item->path);
                            $menus->where('path', 'not like', $item->path . '/%');
                        }
                    }

                    $menus->whereNotIn('path', function ($query) {
                        $query->select('path')->from('acl_menus')->where('enabled', false);
                    })->orderBy('path');

                    $menus = $menus->get()->filter(function ($item) {
                        return $this->aclIsEnabledPath($item->path);
                    });

                    $keys = [];
                    foreach ($menus as $item) {
                        $pathTree = $this->aclPathToFilter($item->path);
                        foreach ($pathTree as $pathKey) {
                            if ($pathKey != $item->path and !in_array($pathKey, $keys)) {
                                $keys[] = $pathKey;
                                $data->add(AclMenu::where('path', $pathKey)->get()->first());
                            }
                        }
                        if (!in_array($item->path, $keys)) {
                            $keys[] = $pathKey;
                            $data->add($item);
                        }
                    }
                }
            }
        }

        return $data;
    }
}
