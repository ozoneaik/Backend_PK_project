<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'emp_no' => '1',
             'emp_name' => 'emp_name',
             'email' => '502365@gmail.com',
             'emp_role' => 'admin',
             'name' => 'Phuwadech',
             'emp_status' => 'active',
             'authcode' => 'authcode',
             'incentive' => 'incentive',
             'password' => Hash::make('1111'),
         ]);

    }
}
