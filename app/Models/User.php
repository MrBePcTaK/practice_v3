<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'tg_title_name',
        'tg_username',
        'tg_chat_id',
        'first_name',
        'last_name',
        'active',
        'notification',
        'deleted_at',
        'updated_at',
    ];

    public function reports()
    {
        return $this->hasMany('App\Models\Report');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderToday(): Order
    {
        return $this->orders()->where('date', now()->format('Y-m-d'))->first();
    }

    public static function scopeActive($query)
    {
        $query->where('active', 1);
    }

    /**
     * Получаем юзеров (активные, неактивные, удаленные)
     * @param string $active
     * @return User $users
     */
    public static function getUsers(string $active = 'active')
    {
        switch ($active) {
            case 'active':
                $users = self::where('active', 1)->get();
                break;
            case 'not_active':
                $users = self::where('active', 0)->get();
                break;
            case 'not_tracked':
                $users = self::onlyTrashed();
                break;
            case 'all':
                $users = self::get();
                break;
        }
            return $users;
    }

    /**
     * Получить список юзеров от которых ожидаем отчет
     * @param $reports
     * @param $users
     * @return \Illuminate\Support\Collection
     */
    public static function debtors($reports, $users)
    {
        $users_today = array_column($reports->toArray(), 'user_id');

        $debtors = array_filter($users->toArray(), function ($item) use ($users_today) {

            if (in_array($item['id'], $users_today) !== true && $item['active'] == 1) {
                return true;
            }
            else {
                return false;
            }
        });

        return collect(array_values($debtors));
    }
}
