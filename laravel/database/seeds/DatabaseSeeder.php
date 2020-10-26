<?php

use Illuminate\Database\Seeder;

use App\Partidas;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        for ($i=0; $i < 15000; $i++) {
            Partidas::create();
        }
    }
}
