<?php

namespace App\Http\Controllers;

use App\Helpers\libraries\TelegramBot;
use App\Helpers\TelegramHelpers;
use App\Models\User;
use App\Models\UserWaitAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Получить список пользователей ожидающих доступ к боту (Для Ajax)
     */
    public function usersWaitAccess(Request $request)
    {
        $wait_users = UserWaitAccess::get();

        return response()->json($wait_users);
    }

    /**
     * Разрешаем юзеру доступ к боту
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accessChange(Request $request)
    {
        $validate = $request->validate([
            'users' => ['required', 'array'],
        ]);

        $users = $request->users;

        foreach ($users as $key => $user_id) {

            /* Получаем всю информацию о пользователе */

            $user_wait = UserWaitAccess::find($user_id);
            $data_user = collect($user_wait->toArray())->except('id');
            $data_user['created_at'] = now();
            $data_user['updated_at'] = now();

            if (!empty($data_user)) {

                /* Удаляем юзера из таблицы доступов и записываем в юзеры */

                $isset_user = User::withTrashed()->where('tg_chat_id', $data_user['tg_chat_id'])->first();

                if ($isset_user) {
                    $isset_user->restore();
                } else {
                    User::insert($data_user->toArray());
                }

                $user_wait->delete();

                $keyboard = TelegramHelpers::startKeyboard();
                TelegramHelpers::sendMessageBot('Доступ к боту разрешен', $data_user['tg_chat_id'], $keyboard);
            }
        }
        return redirect()->route('home');
    }

    /**
     * Получение всех юзеров
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function persons()
    {
        $users = User::get();

        return view('persons', ['users' => $users]);
    }

    /**
     * Управление сотрудниками(изменение, удаление)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function personAction(Request $request)
    {
        $validator = Validator::make(['id' => $request->id], [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Ошибка идентификатора пользователя');
        }

        $user = User::find($request->id);

        if (!$user) {
            return redirect()->back()->with('error', 'Пользователь не найден');
        }

        if ($request->get('delete_person')) {

            $user->reports()->delete();
            $user->delete($request->id);

            return redirect()->back()->with('success', "Пользователь $user->first_name $user->last_name удален");
        }

        if ($request->get('edit_person')) {

            $validator = Validator::make($request->all(), [
                'tg_title_name' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', 'Поля не могут оставаться пустыми');
            }

            $user->update($request->all());

            return redirect()->back()->with('success', "Данные пользователя $user->first_name $user->last_name изменены");
        }

        return redirect()->back()->with('error', 'Переданы неверные параметры');
    }

    /**
     * Изменение статуса сотрудника(активный, неактивный) - для мониторинга отчетов. Ждем от него отчет или нет
     * @param Request $request
     */
    public function changeUserStatus(Request $request)
    {
        $validate = $request->validate([
            'status' => ['required'],
            'user_id' => ['required'],
        ]);

        $status = $request->status == "true" ? 1 : 0;
        $user   = User::where('id', $request->user_id)->first();

        if ($user) {
            $user->active = $status;
            $user->save();
        }
    }
}
