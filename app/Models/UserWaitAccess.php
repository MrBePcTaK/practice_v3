<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWaitAccess extends Model
{
    protected $table = 'users_wait_access';
    protected $fillable = [
        'tg_username',
        'tg_title_name',
        'tg_chat_id'
    ];
}
