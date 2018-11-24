<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class FunctionalityRelationsTableSeeder extends Seeder {

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // $faker = Faker::create();
        Eloquent::unguard();
        FunctionalityRelation::truncate(); // 清空表

        $mainMenus = Functionality::where('action', '=', 'index')->get();
        $mainIds = [];
        $f_id = null;
        $fr_id = null;
        $modelNames = [
            'Admin_AdminUserResource'               => 'AdminUser',
            'Admin_UserResource'                    => 'User',
            'Admin_FunctionalityRelationResource'   => 'Relations',
            'Admin_FunctionalityResource'           => 'Functionality',
            'Admin_AdminMenuResource'               => 'AdminMenu',
            'Admin_AdminRoleResource'               => 'AdminRole',
        ];
        $arr = ['Admin_FunctionalityResource', 'Admin_AdminMenuResource'];
        foreach ($mainMenus as $key => $value) {
            array_push($mainIds, $value->id);
            if (in_array($value->controller, $arr)) {
                FunctionalityRelation::create([
                    'functionality_id'   => $value->id,
                    'r_functionality_id' => $value->id,
                    'for_page'           => 0,
                    'for_item'           => 1,
                    'label'              => 'Sub ' . $value->controller, // ucfirst($value->action)
                ]);
            }
            if ($value->controller == 'Admin_FunctionalityResource') $f_id = $value->id;
            if ($value->controller == 'Admin_FunctionalityRelationResource') $fr_id = $value->id;
        }
        FunctionalityRelation::create([
            'functionality_id'   => $f_id,
            'r_functionality_id' => $fr_id,
            'for_page'           => 0,
            'for_item'           => 1,
            'label'              => 'Relations'
        ]);
        $functionalities = Functionality::whereNotIn('parent_id', [0,1,2,3])->get();
        foreach ($functionalities as $key => $value) {
            if (in_array($value->parent_id, $mainIds)) {
                FunctionalityRelation::create([
                    'functionality_id'   => $value->parent_id,
                    'r_functionality_id' => $value->id,
                    'for_page'           => $value->action == 'create' ? 1 : 0,
                    'for_item'           => $value->action == 'create' ? 0 : 1,
                    'label'              => ucfirst($value->action) // . ' ' . $modelNames[$value->controller]
                ]);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}