<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        //users
        \App\Models\User::factory()->create([
            'email' => 'C001@gmail.com',
            'emp_role' => 'QC',
            'name' => 'ภูวเดช พาณิชยโสภา',
            'emp_status' => 'active',
            'authcode' => 'C001',
            'incentive' => 'incentive',
            'password' => Hash::make('1111'),
        ]);
        \App\Models\User::factory()->create([
            'email' => 'C002@gmail.com',
            'emp_role' => 'HR',
            'name' => 'ว่าที่ HR คนใหม่',
            'emp_status' => 'active',
            'authcode' => 'C002',
            'incentive' => 'incentive',
            'password' => Hash::make('1111'),
        ]);
    }
}
