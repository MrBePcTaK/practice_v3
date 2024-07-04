<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report',
        'user_id',
        'deleted_at',
        'updated_at',
    ];

    /**
     * Получить юзера для отчета
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Получить отчеты за сегодняшний день
     * @param $query
     * @return mixed
     */
    public static function scopeGetReportsToday($query)
    {
        $query->whereDate('created_at', Carbon::today());
        return $query;
    }
}
