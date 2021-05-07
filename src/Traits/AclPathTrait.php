<?php

namespace Antares\Acl\Traits;

use Antares\Acl\Http\AclHttpErrors;
use Antares\Acl\Models\AclMenu;
use Antares\Http\JsonResponse;

trait AclPathTrait
{
    /**
     * Proper trim of path
     *
     * @param string $path
     * @return string
     */
    public function aclTrimPath($path)
    {
        return rtrim(trim($path), '/');
    }

    /**
     * Menu path protected property
     *
     * @var string
     */
    protected $menuPath;

    /**
     * Menu path property acessor
     *
     * @return string
     */
    public function menuPath()
    {
        return $this->aclTrimPath($this->menuPath);
    }

    /**
     * Get filter from given path
     *
     * @param string|array $path
     * @return array
     */
    public function aclPathToFilter($path)
    {
        if (is_array($path)) {
            $filter = $path;
        } else {
            $filter = [];
            $item = '';

            $pieces = explode('/', $path);
            while (count($pieces) > 0) {
                $item = !empty($item) ? ($item . '/') : '';
                $item .= array_shift($pieces);
                $filter[] = $item;
            }
        }

        return $filter;
    }

    /**
     * Validate menu path existance
     *
     * @param string $path
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function aclPathExists($path = '')
    {
        $path = $this->aclTrimPath($path);
        if ($path) {
            $menu = AclMenu::where('path', $path)->get()->first();
            if (empty($menu)) {
                return JsonResponse::error(AclHttpErrors::error(AclHttpErrors::MENU_PATH_NOT_FOUND), null, [
                    'path' => $path,
                ]);
            }
        }

        return true;
    }

    /**
     * Check whether the supplied path is enabled
     *
     * @param strning path
     * @return boolean
     */
    public function aclIsEnabledPath($path)
    {
        $isEnabled = false;

        $path = $this->aclTrimPath($path);
        if ($path) {
            $menus = AclMenu::whereIn('path', $this->aclPathToFilter($path))->where('enabled', false)->get();
            $isEnabled = $menus->isEmpty();
        }

        return $isEnabled;
    }
}
