<?php

namespace Database\Seeders;

use App\Models\Remark;
use Illuminate\Database\Seeder;

class RemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->remarks() as $remark) {
            Remark::create($remark);
        }
    }

    private function remarks()
    {
        return [
            [
                'code' => 'refused',
                'title' => 'Отказался',
            ],
            [
                'code' => 'waiting_for_payment',
                'title' => 'Жду оплату',
            ],
            [
                'code' => 'trial',
                'title' => 'Пробую',
            ]
        ];
    }
}
