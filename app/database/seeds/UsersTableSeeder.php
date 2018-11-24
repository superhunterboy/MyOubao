<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

// class UsersTableSeeder extends Seeder {

// 	public function run()
// 	{
// 		$faker = Faker::create();

// 		foreach(range(1, 10) as $index)
// 		{
// 			User::create([

// 			]);
// 		}
// 	}

// }


class UsersTableSeeder extends Seeder {
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        User::truncate(); // 清空表
        $password = Hash::make('111111');
        User::create(array(
            'username'   => 'Agent',
            'password'   => $password,
            // 'password_confirmation' => $password,
            'fpassword'  => $password,
            'parent_id'  => 0,
            'is_agent'   => 1,
            'parent_str' => '',
            'username'   => 'Agent',
            'nickname'   => 'Agent',
            'email'      => 'test@test.com'
        ));
        sleep(1);
        foreach(range(1, 10) as $index)
        // for ($index = 1; $index < 10; $index++)
        {
            User::create(array(
                'username'   => 'User_' . $index,
                'password'   => $password,
                // 'password_confirmation' => $password,
                'fpassword'  => $password,
                'parent_id'  => 1,
                'parent_str' => 'Agent',
                'is_agent'   => 0,
                'nickname'   => 'User_' . $index,
                'email'      => 'test@test.com'
            ));
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}