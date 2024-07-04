<?php

namespace App\Helpers\Telegram\Reports;

use App\Helpers\libraries\TelegramBot;
use App\Helpers\Telegram\Bots\BotsInterface;
use App\Helpers\TelegramHelpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportBot implements BotsInterface
{
    private $telegramBot;

    public function webhook($response)
    {
        try {

            Log::info('ERRORRRRR: ' , $response);

            $this->telegramBot = new TelegramBot($response);
            $chat_id_group = $this->telegramBot->getGroupChatId();
            $chat_id = $this->telegramBot->getChatId();
            $text = $this->telegramBot->getMessageText();

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
//                        $this->telegramBot->routerButton($button);
                        TelegramHelpers::routerButton($button, $this->telegramBot);
                    }
                } else {
                    /* Проверяем имеет ли доступ к боту данный юзер */

                    $invite = TelegramHelpers::checkInvite($this->telegramBot);

                    if (!$invite) {
                        return response("Юзер ещё не получил доступ", 200);
                    }

                    /* Проверяем, возможно это команда */

                    $command = TelegramHelpers::validatorCommand($this->telegramBot);

                    if ($command != false) {
                        TelegramHelpers::routerCommand($command, $this->telegramBot);
                    }
                }
            } else {
                /* Иначе удаляем файл сессии */

                Storage::disk('public')->delete('telegram-sessions/chat-sessions/' . $chat_id);
            }

            return response('OK', 200);
        } catch (\Exception $exception) {
            $this->telegramBot->sendMessage($exception->getMessage());
            return response('Exception ' . $exception->getMessage(), 200);
        }
    }

}