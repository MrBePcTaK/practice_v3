<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserWaitAccess;

class UsersWaitSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            array('tg_title_name' => 'New', 'tg_username' => 'newUser', 'tg_chat_id' => 713653, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
            array('tg_title_name' => 'New2', 'tg_username' => 'newUser2', 'tg_chat_id' => 713654, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d H:i:s')),
        ];

        UserWaitAccess::insert($users);
    }
}
