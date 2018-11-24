<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class AdminMenuTableSeeder extends Seeder {

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        AdminMenu::truncate(); // 清空表

        $menus = [
            'Developments' => [
                'Admin_FunctionalityResource' => 'index',
                'Admin_FunctionalityRelationResource' => 'index',
                'Admin_AdminMenuResource'     => 'index',

            ],
            // 'Roles Management' => [
            //     'Admin_AdminRoleResource'     => 'index',
            // ],
            'Users' => [
                'Admin_UserResource'          => 'index',
            ],
            'AdminUsers' => [
                'Admin_AdminRoleResource'     => 'index',
                'Admin_AdminUserResource'     => 'index',
            ]
        ];
        $listPageNames = [
            'Admin_AdminUserResource'               => 'AdminUser List',
            'Admin_UserResource'                    => 'User List',
            'Admin_FunctionalityRelationResource'   => 'Functionality Relations',
            'Admin_FunctionalityResource'           => 'Functionality Management',
            'Admin_AdminMenuResource'               => 'AdminMenu List',
            'Admin_AdminRoleResource'               => 'AdminRole List',
        ];
        $main_fun_ids = Functionality::where('action', '=', '(main)')->get(['title', 'id']);
        $fun_ids = Functionality::where('action', '=', 'index')->get(['controller', 'id']);
        $mainFunIds = [];
        $ids = [];
        foreach ($main_fun_ids as $key => $value) {
            $mainFunIds[ $value->title ] = $value->id;
        }
        foreach ($fun_ids as $key => $value) {
            $ids[ $value->controller ] = $value->id;
        }
        $index = 1;
        foreach ($menus as $key => $menu) {
            AdminMenu::create([
                'title'         => $key,
                'parent_id'     => 0,
                'functionality_id' => $mainFunIds[$key],
                // 'controller'    => '(main)',
                // 'action'        => '(main)',
                'description'   => $key

            ]);
            foreach ($menu as $controller => $action) {
                AdminMenu::create([
                    'title'         => $listPageNames[$controller], // $controller . ' ' . $action,
                    'parent_id'     => $index,
                    'functionality_id' => $ids[$controller],
                    'controller'    => $controller,
                    'action'        => $action,
                    'description'   => $listPageNames[$controller]
                ]);
            }
            $index += count($menu) + 1;
            // if ($key != 'Developments') $index += 2;
            // else $index += 4;

        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}