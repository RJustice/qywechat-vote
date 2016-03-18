<?php

use Illuminate\Database\Seeder;

class CreateUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $user = \App\User::create([
            'password' => Hash::make('Zw2016!!'),
            'email' => 'mingyiAdmin',
            'name' => '超级管理员',
        ]);
    }
}
