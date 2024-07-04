<?php

namespace App\Http\Controllers;

use App\Models\UserWaitAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /* Получаем отчеты отправленные за сегодня */
        $reports = Report::with('user')->getReportsToday()->get();

        /* Получить юзеров, от которых ожидаем отчет */
        $users = User::getUsers('all');

        /* Проверяем есть ли юзеры ожидающие доступ */
        $wait_users = UserWaitAccess::get();

        /* Получаем список пользователей, от которых ещё ожидаем отчёт */
        $debtors = User::debtors($reports, $users);

        return view('home', [
            'reports' => $reports,
            'debtors' => $debtors,
            'wait_users' => $wait_users,
            'users' => $users,
        ]);
    }
}
