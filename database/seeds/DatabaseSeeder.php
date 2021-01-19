<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      DB::table('users')->delete();

      $users = array(
              ['name' => 'Jesus Oroño', 'email' => 'oroxo@gmail.com', 'password' => Hash::make('19211894')],
              ['name' => 'Chris Sevilleja', 'email' => 'chris@scotch.io', 'password' => Hash::make('secret')],
              ['name' => 'Holly Lloyd', 'email' => 'holly@scotch.io', 'password' => Hash::make('secret')],
              ['name' => 'Adnan Kukic', 'email' => 'adnan@scotch.io', 'password' => Hash::make('secret')],
      );

      // Loop through each user above and create the record for them in the database
      foreach ($users as $user)
      {
          User::create($user);
      }

        // $this->call(UsersTableSeeder::class);
        //$this->call(CeldasTableSeeder::class);
    }
}
