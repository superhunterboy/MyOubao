<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class FunctionalitiesTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        Functionality::truncate(); // 清空表
        $mainControllers = [
            'Developments',
            'Users',
            'AdminUsers',
        ];
        $controllers = [
            // 'Developments' => [
                'Admin_FunctionalityRelationResource' => ['view', 'create', 'edit', 'destroy'],
                'Admin_FunctionalityResource' => ['view', 'create', 'edit', 'destroy'],
                'Admin_AdminMenuResource'     => ['view', 'create', 'edit', 'destroy'],
            // ],
            // 'Users' => [
                'Admin_UserResource'          => ['view', 'create', 'edit', 'destroy'],
            // ],
            // 'AdminUsers' => [
                'Admin_AdminUserResource'     => ['view', 'create', 'edit', 'destroy'],
                'Admin_AdminRoleResource'     => ['view', 'create', 'edit', 'destroy', 'showBindingUser', 'updateUserBinding', 'showFunctionalityBinding', 'updateFunctionalityBinding'],
            // ],
        ];
        $indexes = [
            'Admin_FunctionalityRelationResource' => 1,
            'Admin_FunctionalityResource' => 1,
            'Admin_AdminMenuResource' => 1,
            'Admin_UserResource' => 2,
            'Admin_AdminUserResource' => 3,
            'Admin_AdminRoleResource' => 3,
        ];
        $listPageNames = [
            'Admin_AdminUserResource'               => 'AdminUser List',
            'Admin_UserResource'                    => 'User List',
            'Admin_FunctionalityRelationResource'   => 'Functionality Relations',
            'Admin_FunctionalityResource'           => 'Functionality Management',
            'Admin_AdminMenuResource'               => 'AdminMenu List',
            'Admin_AdminRoleResource'               => 'AdminRole List',
        ];
        $modelNames = [
            'Admin_AdminUserResource'               => 'AdminUser',
            'Admin_UserResource'                    => 'User',
            'Admin_FunctionalityRelationResource'   => 'Relations',
            'Admin_FunctionalityResource'           => 'Functionality',
            'Admin_AdminMenuResource'               => 'AdminMenu',
            'Admin_AdminRoleResource'               => 'AdminRole',
        ];
        foreach ($mainControllers as $mname) {
            Functionality::create([
                'title'         => $mname,
                'controller'    => '(main)',
                'action'        => '(main)',
                'description'   => $mname,
                'realm'         => 1,
                'menu'          => 1
            ]);
        }
        $index = 4;
        // foreach ($controllers as $mname => $fun) {
        //     Functionality::create([
        //         'title'         => $mname,
        //         'controller'    => '(main)',
        //         'action'        => '(main)',
        //         'description'   => $mname,
        //         'realm'         => 1,
        //         'menu'          => 1
        //     ]);
            // $count = count($fun);
            foreach ($controllers as $cname => $controller) {
                Functionality::create([
                    'title'         => $listPageNames[$cname],
                    'controller'    => $cname,
                    'action'        => 'index',
                    'description'   => $listPageNames[$cname],
                    'parent_id'     => $indexes[$cname],
                    'parent_str'    => $indexes[$cname],
                    'realm'         => 1,
                    'menu'          => 1
                ]);
                // $index += 1;
                foreach ($controller as $aname => $action) {
                    Functionality::create([
                        'title'         => ucfirst($action) . ' ' . $modelNames[$cname],
                        'controller'    => $cname,
                        'action'        => $action,
                        'description'   => ucfirst($action) . ' ' . $modelNames[$cname],
                        'parent_id'     => $index,
                        'parent_str'    => $indexes[$cname] . ',' . $index,
                        'realm'         => 1
                    ]);
                    // $index += 1;
                }
                // if (in_array($cname, ['Admin_AdminMenuResource', 'Admin_FunctionalityResource'])) $index += 5;
                if ($cname == 'Admin_AdminRoleResource') $index += 9;
                else $index += 5;
                // $index--;

            }
            // $index += 1;
        // }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}