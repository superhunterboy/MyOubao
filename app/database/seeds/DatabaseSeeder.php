<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		Eloquent::unguard();

		$this->call('RolesTableSeeder');

		$this->call('AdminUsersTableSeeder');
		$this->call('UsersTableSeeder');

        $this->call('FunctionalitiesTableSeeder');

        sleep(3);
        $this->call('FunctionalityRelationsTableSeeder');
        $this->call('FunctionalityRoleTableSeeder');
        $this->call('AdminMenuTableSeeder');

        sleep(5);
        $this->call('AdminUserRoleTableSeeder');

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

	}

}
