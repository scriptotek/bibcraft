<?php

class LibrariansTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		//DB::table('users')->truncate();

		$librarians = array(
			array(
				'name' => 'Dan Michael O. Heggø',
				'username' => 'dmheggo@ub.uio.no',
				'password' => Hash::make('admin'),
				'superpowers' => true,
				'password_changed_at' => new DateTime,
				'created_at' => new DateTime,
				'updated_at' => new DateTime,
				'activated_at' => new DateTime,
			),
		);

		// Uncomment the below to run the seeder
		DB::table('librarians')->insert($librarians);
	}

}
