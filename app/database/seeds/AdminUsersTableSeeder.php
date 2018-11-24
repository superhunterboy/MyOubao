<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class AdminUsersTableSeeder extends Seeder {

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // $faker = Faker::create();
        //
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        AdminUser::truncate(); // 清空表
        $password = Hash::make('111111');

        AdminUser::create([
            'username'  => 'System',
            'password'  => $password,
            // 'password_confirmation' => $password,
            'name'      => 'System',
            // 'is_root'   => 1,
            // 'role_id'   => 1,
        ]);
        foreach(range(1, 10) as $index)
        // for ($index = 1; $index < 10; $index++)
        {
            AdminUser::create([
            'username'  => 'User_' . $index,
            'password'  => $password,
            // 'password_confirmation' => $password,
            'name'      => 'User_' . $index,
            // 'is_root'   => 0,
            // 'role_id'   => 1,
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}