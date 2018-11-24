<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

// class RolesTableSeeder extends Seeder {

// 	public function run()
// 	{
// 		$faker = Faker::create();

// 		foreach(range(1, 10) as $index)
// 		{
// 			Role::create([

// 			]);
// 		}
// 	}

// }

class RolesTableSeeder extends Seeder {
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        Role::truncate(); // 清空表

        $arr = [
            'Administrators'    => 'Administrators',
            'Everyone'          => 'Everyone',
            'Deny'              => 'Deny',
            'Users'             => 'Users',
            'Normal Admin'      => 'Normal Admin',
            'GRS Manager'       => 'GRS Manager',
            'Source Auditor'    => 'Source Auditor',
            'Monitor'           => 'Monitor',
            'Test'              => 'Test',
        ];
        foreach ($arr as $key => $value) {
            Role::create(array(
                'name' => $key,
                'rights' => '',
                'description' => $value
            ));
        }

        // Role::create(array(
        //     'name' => 'Administrators',
        //     'rights' => '',
        //     'description' => '系统管理员权限'
        // ));
        // sleep(1);
        // Role::create(array(
        //     'name' => 'Normal Admin',
        //     'rights' => '1',
        //     'description' => '普通管理员权限'
        // ));
        // sleep(1)
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}