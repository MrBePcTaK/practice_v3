<?php

namespace App\Http\Controllers;

use App\Helpers\libraries\TelegramBot;
use App\Helpers\TelegramHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramController extends Controller
{
    private $telegram_bot;
    private $message_array;

    public function __construct(Request $request)
    {
        /**
         * $request->json()->all() - json сообщение из телеграмм
         */

        $this->message_array = $request->json()->all();
        $this->telegram_bot = new TelegramBot($this->message_array);
    }

    /**
     * Отправить сегодняшний общий отчет в чат телеграм
     */
    public function sendDocumentToday()
    {
        $today = Carbon::today()->unix();

        $files = Storage::disk('public')->allFiles('daily-reports');

        foreach ($files as $file) {
            $tmp = explode('/', $file);

            if ((int)$tmp[1] >= $today) {

                $file_url = Storage::disk('public')->path('daily-reports/' . $tmp[1]);
                $res = TelegramHelpers::sendDocument($file_url, "Общий отчет " . date('Y-m-d', time()));
                break;
            }
        }

        if (!isset($res)) {
            Log::info('За ' . date('Y-m-d', time()) . 'общего отчета не было');
        }
    }

    public function index()
    {
        $route = route('telegram-webhook');

        if (config('app.env') == 'production') {
            $response = $this->telegram_bot->setWebhook($route);
        } else {
            $response = $this->telegram_bot->setWebhook('https://0cd6-84-22-145-180.ngrok-free.app/api/telegram/get-message');
        }

        if ($response->status == 200) {
            dd('Webhook установлен URL = ' . $route);
        } else {
            dd('Webhook url не удалось установить');
        }
    }

    /**
     * Вебхук для телеграм сообщения
     * @param Request $request
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        try {
            $chat_id_group = $this->telegram_bot->getGroupChatId();
            $chat_id       = $this->telegram_bot->getChatId();
            $text          = $this->telegram_bot->getMessageText();

            if ((int)$chat_id_group === (int)$chat_id) {
                return response("Сообщение из группы", 200);
            }

            /* Проверка не отменяет ли пользователь действие */

            $result_exit = TelegramHelpers::checkExit($text);

            if ($result_exit == false) {

                /* Если это не отмена идём дальше и проверяем есть ли сессия пользователя */

                $session = TelegramHelpers::checkSession($chat_id);

                if ($session) {

                    /* Проверяем какая кнопка была нажата */

                    $button = TelegramHelpers::checkButton($chat_id);

                    if ($button) {
//                        $this->telegram_bot->routerButton($button);
                        TelegramHelpers::routerButton($button, $this->telegram_bot);
                    }

                } else {

                    /* Проверяем имеет ли доступ к боту данный юзер */

                    $invite = TelegramHelpers::checkInvite($this->telegram_bot);

                    if (!$invite) {
                        return response("Юзер ещё не получил доступ", 200);
                    }

                    /* Проверяем, возможно это команда */

                    $command = TelegramHelpers::validatorCommand($this->telegram_bot);

                    if ($command != false) {
                        TelegramHelpers::routerCommand($command, $this->telegram_bot);
                    }
                }

            } else {

                /* Иначе удаляем файл сессии */

                Storage::disk('public')->delete('telegram-sessions/chat-sessions/' . $chat_id);
            }

            return response('OK', 200);

        } catch (\Exception $exception) {
            $this->telegram_bot->sendMessage($exception->getMessage());
            return response('Exception ' . $exception->getMessage(), 200);
        }
    }
}
