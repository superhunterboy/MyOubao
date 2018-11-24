<?php

class AdminUserRoleTableSeeder extends Seeder {
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        DB::table('admin_user_role')->truncate(); // 清空表
        $usersRole = Role::where('name', '=', 'Users')->first()->id;
        $adminRole = Role::where('name', '=', 'Administrators')->first()->id;
        $users = AdminUser::all();
        foreach ($users as $key => $user) {
            $arr = ['admin_user_id' => $user->id, 'created_at' => new DateTime, 'updated_at' => new DateTime];
            if ($user->username == 'System') $arr['role_id'] = $adminRole;
            else $arr['role_id'] = $usersRole;
            DB::table('admin_user_role')->insert($arr);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}