<?php

namespace Antares\Acl\Tests\Traits;

use Antares\Acl\Models\AclGroup;
use Antares\Acl\Models\AclGroupRight;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\AclUserGroup;
use Antares\Acl\Models\AclUserRight;
use Antares\Acl\Models\User;

trait SeedDatabaseTrait
{
    private function seedUsers($amount = 30)
    {
        return User::factory()->count($amount)->create();
    }

    private function seedSessions($amount = 20)
    {
        return AclSession::factory()->count($amount)->create();
    }

    private function seedMenus($menuAmount = 5, $pathAmount = 3, $optionAmount = 3, $actionAmount = 3)
    {
        $items = [];
        $items[] = AclMenu::create([
            'path' => 'root',
            'description' => 'Root Menu',
        ]);
        for ($menu = 1; $menu <= $menuAmount; $menu++) {
            $menuLabel = str_pad($menu, 2, '0', STR_PAD_LEFT);
            $menuPath = 'root/menu-' . $menuLabel;
            $menuDescription = 'Menu ' . $menuLabel;
            $items[] = AclMenu::create([
                'path' => $menuPath,
                'description' => $menuDescription,
                'enabled' => ($menu != 2),
            ]);
            for ($path = 1; $path <= $pathAmount; $path++) {
                $pathLabel = str_pad($path, 2, '0', STR_PAD_LEFT);
                $pathPath = $menuPath . '/path-' . $pathLabel;
                $pathDescription = $menuDescription . ' : Path ' . $pathLabel;
                $items[] = AclMenu::create([
                    'path' => $pathPath,
                    'description' => $pathDescription,
                    'enabled' => ($path != 2),
                ]);
                for ($option = 1; $option <= $optionAmount; $option++) {
                    $optionLabel = str_pad($option, 2, '0', STR_PAD_LEFT);
                    $optionPath = $pathPath . '/option-' . $optionLabel;
                    $optionDescription = $pathDescription . ' : Option ' . $optionLabel;
                    $items[] = AclMenu::create([
                        'path' => $optionPath,
                        'description' => $optionDescription,
                        'enabled' => ($option != 2),
                    ]);
                    for ($action = 1; $action <= $actionAmount; $action++) {
                        $actionLabel = str_pad($action, 2, '0', STR_PAD_LEFT);
                        $actionPath = $optionPath . '/action-' . $actionLabel;
                        $actionDescription = $optionDescription . ' : Action ' . $actionLabel;
                        $items[] = AclMenu::create([
                            'path' => $actionPath,
                            'description' => $actionDescription,
                            'enabled' => ($action != 2),
                        ]);
                    }
                }
            }
        }
        return $items;
    }

    private function seedGroups($amount = 5)
    {
        $items = [];
        for ($group = 1; $group <= $amount; $group++) {
            $items[] = AclGroup::create([
                'name' => 'Group ' . str_pad($group, 2, '0', STR_PAD_LEFT),
                'enabled' => ($group != 2),
            ]);
        }
        return $items;
    }

    private function seedGroupRights($amount = 5)
    {
        $items = [];
        for ($item = 1; $item <= $amount; $item++) {
            $items[] = AclGroupRight::create([
                'group_id' => $item,
                'menu_id' => AclMenu::where('path', 'root/menu-' . str_pad($item, 2, '0', STR_PAD_LEFT))->firstOrFail()->id,
                'right' => 1, // allow
                'enabled' => true,
            ]);
        }
        return $items;
    }

    private function seedUserGroups($amount = 5)
    {
        $items = [];
        for ($item = 1; $item <= $amount; $item++) {
            $items[] = AclUserGroup::create([
                'user_id' => User::findOrFail($item + 10)->id,
                'group_id' => AclGroup::findOrFail($item)->id,
            ]);
        }
        return $items;
    }

    private function seedAdminAcls()
    {
        AclGroup::create([
            'id' => 999999,
            'name' => 'Admin Group',
            'enabled' => true,
        ]);

        AclUserGroup::create([
            'user_id' => 1,
            'group_id' => 999999,
        ]);

        AclGroupRight::create([
            'group_id' => 999999,
            'menu_id' => AclMenu::where('path', 'root')->firstOrFail()->id,
            'right' => 1, // allow
            'enabled' => true,
        ]);
    }

    private function seedDatabase()
    {
        $this->seedUsers(30);
        $this->seedSessions();
        $this->seedMenus(5, 3, 3, 3);
        $this->seedGroups(5);
        $this->seedGroupRights(5);
        $this->seedUserGroups(5);
        $this->seedAdminAcls();

        AclGroup::create([
            'id' => 51,
            'name' => 'Group 51',
            'enabled' => true,
        ]);
        AclGroupRight::create([
            'group_id' => 51,
            'menu_id' => AclMenu::where('path', 'root/menu-05')->firstOrFail()->id,
            'right' => 1, // allow
            'enabled' => true,
        ]);
        AclGroupRight::create([
            'group_id' => 51,
            'menu_id' => AclMenu::where('path', 'root/menu-01')->firstOrFail()->id,
            'right' => 0, // deny
            'enabled' => false,
        ]);
        AclGroupRight::create([
            'group_id' => 51,
            'menu_id' => AclMenu::where('path', 'root/menu-03/path-03/option-03')->firstOrFail()->id,
            'right' => 0, // deny
            'enabled' => true,
        ]);

        AclUserRight::create([
            'user_id' => 21,
            'menu_id' => AclMenu::where('path', 'root/menu-04/path-03/option-03')->firstOrFail()->id,
            'right' => 1, // allow
            'enabled' => true,
        ]);
        AclUserRight::create([
            'user_id' => 21,
            'menu_id' => AclMenu::where('path', 'root/menu-04/path-03/option-02')->firstOrFail()->id,
            'right' => 1, // allow
            'enabled' => true,
        ]);
        AclUserRight::create([
            'user_id' => 21,
            'menu_id' => AclMenu::where('path', 'root/menu-04/path-01')->firstOrFail()->id,
            'right' => 0, // deny
            'enabled' => true,
        ]);
        AclUserRight::create([
            'user_id' => 21,
            'menu_id' => AclMenu::where('path', 'root/menu-04/path-01')->firstOrFail()->id,
            'right' => 1, // allow
            'enabled' => true,
        ]);

        AclUserGroup::create(['user_id' => User::findOrFail(21)->id, 'group_id' => AclGroup::findOrFail(1)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(21)->id, 'group_id' => AclGroup::findOrFail(2)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(21)->id, 'group_id' => AclGroup::findOrFail(51)->id]);

        AclUserGroup::create(['user_id' => User::findOrFail(22)->id, 'group_id' => AclGroup::findOrFail(1)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(22)->id, 'group_id' => AclGroup::findOrFail(3)->id]);

        AclUserGroup::create(['user_id' => User::findOrFail(23)->id, 'group_id' => AclGroup::findOrFail(2)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(23)->id, 'group_id' => AclGroup::findOrFail(3)->id]);

        AclUserGroup::create(['user_id' => User::findOrFail(24)->id, 'group_id' => AclGroup::findOrFail(3)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(24)->id, 'group_id' => AclGroup::findOrFail(4)->id]);

        AclUserGroup::create(['user_id' => User::findOrFail(25)->id, 'group_id' => AclGroup::findOrFail(4)->id]);
        AclUserGroup::create(['user_id' => User::findOrFail(25)->id, 'group_id' => AclGroup::findOrFail(5)->id]);

        $this->assertCount(31, User::all());
    }
}
