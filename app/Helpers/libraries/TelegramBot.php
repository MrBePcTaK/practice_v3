<?php

namespace App\Helpers\libraries;


use App\Helpers\TelegramHelpers;
use App\Models\Report;
use App\Models\User;
use App\Models\UserWaitAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;

class TelegramBot
{
    private $base_url = 'https://api.telegram.org/bot';
    private $array_message;
    private $telegram_chat_id;
    private $telegram_token;

    public function __construct($message = null, $tg_chat_id = null)
    {
        $this->telegram_token   = config('bots.bots.food_orders.token');
        $this->chat_id_group    = config('bots.bots.food_orders.chat_id_group');
        $this->array_message    = $message;
        $this->telegram_chat_id = $tg_chat_id ?: $this->getChatId();
    }

    /**
     * Получить chat_id группы
     */
    public function getGroupChatId(): string
    {
        return $this->chat_id_group;
    }

    /**
     * Вывод сообщения на экран(для тестов)
     */
    public function outputMessage()
    {
        dd($this->array_message);
    }

    /**
     * Получение chatId
     * @return string $chat_id
     */
    public function getChatId()
    {
        if (!empty($this->array_message)) {

            if (is_array($this->array_message) && isset($this->array_message['message']['chat']['id'])) {
                if (isset($this->array_message['message']['chat']['id'])) {
                    return $this->array_message['message']['chat']['id'];
                }
            }
        }
        return false;
    }

    /**
     * Получить текста сообщения от юзера из вебхука
     * @return false|mixed
     */
    public function getMessageText()
    {
        if (!empty($this->array_message)) {

            if (is_array($this->array_message) && isset($this->array_message['message']['text'])) {
                if (isset($this->array_message['message']['text'])) {
                    return $this->array_message['message']['text'];
                }
            }
        }
        return false;
    }

    /**
     * Получение username пользователя написавшего в чат
     * @return false|mixed
     */
    public function getUserName()
    {
        if (!empty($this->array_message)) {

            if (is_array($this->array_message) && isset($this->array_message['message']['chat']['username'])) {
                if (isset($this->array_message['message']['chat']['username'])) {
                    return $this->array_message['message']['chat']['username'];
                }
            }
        }
        return false;
    }

    /**
     * Получить telegram first_name (title)
     * @return false|mixed
     */
    public function getFirstName()
    {
        if (!empty($this->array_message)) {

            if (is_array($this->array_message) && isset($this->array_message['message']['chat'])) {
                if (isset($this->array_message['message']['from']['first_name'])) {
                    return $this->array_message['message']['from']['first_name'];
                }
            }
        }
        return false;
    }

    /**
     * Получение идентификатора сообщения
     * @return false|mixed
     */
    public function getMessageId()
    {
        if (!empty($this->array_message)) {

            if (is_array($this->array_message) && isset($this->array_message['message']['message_id'])) {
                if (isset($this->array_message['message']['message_id'])) {
                    return $this->array_message['message']['message_id'];
                }
            }
        }
        return false;
    }

    /**
     * @param $message
     * @param $keyboard
     * Метод отправления сообщения боту /sendMessage
     * @return bool
     */
    public function sendMessage($message, $keyboard = null)
    {
        if ($keyboard != null) {
            $post_fields = [
                'chat_id' => $this->telegram_chat_id,
                'text' => $message,
                'reply_markup' => json_encode($keyboard),
                'disable_web_page_preview' => false,
                'one_time_keyboard' => true,
            ];
        } else {
            $post_fields = [
                'chat_id' => $this->telegram_chat_id,
                'text' => $message,
//            'parse_mode' => 'HTML',
            ];
        }

        $res = Curl::to($this->base_url . $this->telegram_token . '/sendMessage')
            ->withData($post_fields)
            ->post();

        if ($res) {
            return true;
        }

        return false;
    }

    /**
     * @param $text
     * @param $reply_markup
     * Метод отправления сообщения боту с кнопкой /sendMessage
     * @return void
     */
    public function sendButton($text, $reply_markup): void
    {
        Http::post($this->base_url . $this->telegram_token . '/sendMessage', [
            'chat_id' => $this->telegram_chat_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Отправить файл через бота
     */
    public function sendDocument($file)
    {
        $chat_id = $this->chat_id_group;

        try {

            $url = $this->base_url . $this->telegram_token . '/sendDocument';

            $file = new \CURLFile($file, 'text/plain', 'test2.txt');

            return Curl::to($url)
                ->withData([
                    'chat_id' => $chat_id,
                    "caption" => 'Отчёт за ' . date('Y-m-d', time()),
                    "document" => $file,
                ])
                ->withHeader('Content-Type:multipart/form-data')
                ->post();

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    /**
     * Отправить документ в чат
     * @param $file_url
     * @param $file_name
     * @return bool|string
     */
    public function sendDocumentCustom($file_url, $file_name)
    {
        try {

            $chat_id = $this->chat_id_group;

            $curl = curl_init($this->base_url . $this->telegram_token . '/sendDocument');

            $obj_file = new \CURLFile($file_url, 'text/plain', $file_name);

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                "chat_id" => $chat_id,
                "document" => $obj_file,
//                "caption" => 'Отчёт за ' . date('Y-m-d', time())
            ]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

            $resp = curl_exec($curl);
            curl_close($curl);

            return $resp;
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    /**
     * Метод для установки url для вебхука
     * @param $url - урл на который должен придти ответ
     */
    public function setWebhook($url)
    {
        return Curl::to($this->base_url . $this->telegram_token . '/setWebhook')
            ->withData(['url' => $url])
            ->returnResponseObject()
            ->post();
    }

    /**
     * Метод для удаления урл вебхука. В таком случае сообщения можно прочитать методом /getUpdates
     */
    public function deleteWebhook()
    {
        return Curl::to($this->base_url . $this->telegram_token . '/deleteWebhook')
            ->returnResponseObject()
            ->get();
    }

    /**
     * Метод для получения информации о текущем установленном вебхуке
     */
    public function getWebhookInfo()
    {
        return Curl::to($this->base_url . $this->telegram_token . '/getWebhookInfo')
            ->returnResponseObject()
            ->get();
    }

    /**
     * Получение последних сообщений
     */
    public function getUpdates()
    {
        /**
         * Добавить возможность LIMIT и OFFSET, TIMEOUT
         */
        return Curl::to($this->base_url . $this->telegram_token . '/getUpdates')
            ->returnResponseObject()
            ->get();
    }

    /**
     * Получение информации о боте
     */
    public function getInfoBot()
    {
        return Curl::to($this->base_url . $this->telegram_token . '/getMe')
            ->returnResponseObject()
            ->get();
    }
}
