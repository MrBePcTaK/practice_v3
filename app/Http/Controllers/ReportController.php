<?php

namespace App\Http\Controllers;

use App\Helpers\TelegramHelpers;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Страница "отчеты"
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::get();

        return view('reports', ['users' => $users]);
    }

    /**
     * Кнопка "добавить" отчет за сотрудника
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addReport(Request $request)
    {
        $validatedData = $request->validate([
            'report_date' => ['required', 'date'],
            'adding_report_text' => ['required' ],
            'report_user_id' => ['required', 'integer'],
        ]);

        $report = User::find(17)->reports()->whereDate('created_at', Carbon::today()->toDateString())->first();

        if (!$report) {
            Report::insert([
                'report' => $request->adding_report_text,
                'user_id' => $request->report_user_id,
                'created_at' => $request->report_date,
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        } else {
            $old_report = $report->report;

            $report->update([
                'report' => "$old_report \n" . $request->adding_report_text,
                'user_id' => $request->report_user_id,
                'created_at' => $request->report_date,
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }

        return redirect()->back()->with('status_save', 'success');
    }

    /**
     * Ajax кнопки "Посмотреть" (отчеты)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReport(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date'],
        ]);

        /**
         * Получаем и удаленные отчеты и удаленных пользователей для отчетов
         */

        $query = Report::whereBetween('created_at', [$request->start_date, $request->end_date]);

        if (isset($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $reports_by_date = $query->withTrashed()->with(['user' => function($query) {
            $query->withTrashed();
        }])->get();

        return response()->json($reports_by_date);
    }

    /**
     * Ajax кнопки "сохранить" отчет при изменении
     */
    public function editReport(Request $request)
    {
        $validatedData = $request->validate([
            'id' => ['required'],
            'text' => ['required'],
        ]);

        $report = Report::find($request->id);
        $result = $report->update(['report' => $request->text]);

        return response()->json($result);
    }

    /**
     * Сформировать общий отчет сотрудников и отправить в чат файлом
     */
    public function generateGeneralReport()
    {
        $reports     = Report::with('user')->getReportsToday()->get();
        $today_start = Carbon::today()->unix();
        $content     = '';

        if ($reports) {

            $content .= 'Общий отчет от ' . date('Y-m-d', time()) . "\n\n";

            foreach ($reports as $report) {
                $content .= 'Отчет от ' . $report->user->first_name . " " . $report->user->last_name . ":\n" . $report->report . "\n\n" ;
            }

            Storage::disk('public')->put('daily-reports/' . $today_start, $content);

            if (Storage::disk('public')->exists('daily-reports/' . $today_start)) {
                $file_url = Storage::disk('public')->path('daily-reports/' . $today_start);

                /* Отправляем сформированный отчет */

                return TelegramHelpers::sendDocument($file_url, "Общий отчет " . date('Y-m-d', time()).".txt");
            }
        }
    }
}
