<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $reports = [
            array('report' => 'Первый тестовый отчет', 'user_id' => 1, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('report' => 'Второй тестовый отчет', 'user_id' => 1, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('report' => 'Третий тестовый отчет', 'user_id' => 1, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('report' => 'Четвертый тестовый отчет', 'user_id' => 1, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
        ];

        Report::insert($reports);
    }
}
