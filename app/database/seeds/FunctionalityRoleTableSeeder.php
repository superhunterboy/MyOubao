<?php

class FunctionalityRoleTableSeeder extends Seeder {
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // 强制解除 Eloquent::create() 批量赋值限制
        Eloquent::unguard();
        // 用户
        DB::table('functionality_role')->truncate(); // 清空表
        $adminRole = Role::where('name', '=', 'Administrators')->first()->id;
        $userRole = Role::where('name', '=', 'Users')->first()->id;
        $functionalities = Functionality::all();
        foreach ($functionalities as $key => $functionality) {
            $arr = ['functionality_id' => $functionality->id, 'created_at' => new DateTime, 'updated_at' => new DateTime];
            $arr['role_id'] = $adminRole;

            DB::table('functionality_role')->insert($arr);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}