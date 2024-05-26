<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\qc_level;
use App\Models\qc_rate;
use App\Models\qc_time;
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
        //users
        \App\Models\User::factory()->create([
            'emp_no' => '1',
            'emp_name' => 'phuwadech',
            'email' => '502365@gmail.com',
            'emp_role' => 'admin',
            'name' => 'ภูวเดช พาณิชยโสภา',
            'emp_status' => 'active',
            'authcode' => 'authcode',
            'incentive' => 'incentive',
            'password' => Hash::make('1111'),
        ]);

        //qc_level
        $qcLevels = [
            ['le_id' => 1, 'le_code' => 'L001', 'le_name' => 'Very Easy'],
            ['le_id' => 2, 'le_code' => 'L002', 'le_name' => 'Easy'],
            ['le_id' => 3, 'le_code' => 'L003', 'le_name' => 'Middling'],
            ['le_id' => 4, 'le_code' => 'L004', 'le_name' => 'Hard'],
            ['le_id' => 5, 'le_code' => 'L005', 'le_name' => 'Very Hard'],
            ['le_id' => 6, 'le_code' => 'L006', 'le_name' => 'No QC']
        ];
        foreach ($qcLevels as $qcLevel) {
            qc_level::create($qcLevel);
        }

        //qc_time
        $times = [
            ['ti_id' => 1, 'time' => '09:00:00', 'grade' => 'A+'],
            ['ti_id' => 2, 'time' => '08:00:00', 'grade' => 'A'],
            ['ti_id' => 3, 'time' => '07:31:00', 'grade' => 'B+'],
            ['ti_id' => 4, 'time' => '07:00:00', 'grade' => 'C'],
        ];
        foreach ($times as $time) {
            qc_time::create($time);
        }

        //qc_rate
        $qcRates = [
            ['ra_id' => 1, 'le_id' => 1, 'grade' => 'C', 'rate' => 0.0833],
            ['ra_id' => 2, 'le_id' => 1, 'grade' => 'B', 'rate' => 0.1042],
            ['ra_id' => 3, 'le_id' => 1, 'grade' => 'A', 'rate' => 0.1125],
            ['ra_id' => 4, 'le_id' => 1, 'grade' => 'A+', 'rate' => 0.125],

            ['ra_id' => 5, 'le_id' => 2, 'grade' => 'C', 'rate' => 0.125],
            ['ra_id' => 6, 'le_id' => 2, 'grade' => 'B', 'rate' => 0.1563],
            ['ra_id' => 7, 'le_id' => 2, 'grade' => 'A', 'rate' => 0.1688],
            ['ra_id' => 8, 'le_id' => 2, 'grade' => 'A+', 'rate' => 0.1875],

            ['ra_id' => 9, 'le_id' => 3, 'grade' => 'C', 'rate' => 0.1667],
            ['ra_id' => 10, 'le_id' => 3, 'grade' => 'B', 'rate' => 0.2083],
            ['ra_id' => 11, 'le_id' => 3, 'grade' => 'A', 'rate' => 0.225],
            ['ra_id' => 12, 'le_id' => 3, 'grade' => 'A+', 'rate' => 0.25],

            ['ra_id' => 13, 'le_id' => 4, 'grade' => 'C', 'rate' => 0.25],
            ['ra_id' => 14, 'le_id' => 4, 'grade' => 'B', 'rate' => 0.3125],
            ['ra_id' => 15, 'le_id' => 4, 'grade' => 'A', 'rate' => 0.3375],
            ['ra_id' => 16, 'le_id' => 4, 'grade' => 'A+', 'rate' => 0.375],

            ['ra_id' => 17, 'le_id' => 5, 'grade' => 'C', 'rate' => 0.25],
            ['ra_id' => 18, 'le_id' => 5, 'grade' => 'B', 'rate' => 0.3125],
            ['ra_id' => 19, 'le_id' => 5, 'grade' => 'A', 'rate' => 0.3375],
            ['ra_id' => 20, 'le_id' => 5, 'grade' => 'A+', 'rate' => 0.375],
        ];
        foreach ($qcRates as $qcRate) {
            qc_rate::create($qcRate);
        }


    }
}
