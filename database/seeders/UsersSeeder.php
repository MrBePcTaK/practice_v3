<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $users = [
//            array('first_name' => 'Eduard', 'last_name' => 'Rosl', 'tg_title_name' => 'Ed', 'tg_username' => 'Edis', 'tg_chat_id' => 713640, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('first_name' => 'Ivan', 'last_name' => 'Vasilievich', 'tg_title_name' => 'Vasek', 'tg_username' => 'VASIK2321', 'tg_chat_id' => 713641, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('first_name' => 'Petr', 'last_name' => 'Petrovich', 'tg_title_name' => 'PETKA', 'tg_username' => 'pedroOval', 'tg_chat_id' => 713643, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('first_name' => 'Valen', 'last_name' => 'Tine', 'tg_title_name' => 'Valentine', 'tg_username' => 'VALEK123', 'tg_chat_id' => 713644, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
        ];

        User::insert($users);
    }
}
