<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
	public function run()
    {
        // Let's clear the users table first
        // User::truncate();

        $faker = \Faker\Factory::create();

        // And now let's generate a few dozen users for our app:
        for ($i = 0; $i < 10; $i++) {
            \App\User::create([
                'name' => $faker->name,
				'email_verified_at' => $faker->dateTime($max = 'now', $timezone = 'Africa/Lagos'),
				'email' => $faker->unique()->safeEmail,
				'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
				'remember_token' => Str::random(10),
            ]);
        }
    }
}
