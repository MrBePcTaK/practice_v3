<?php


namespace App\Helpers;


use App\Helpers\libraries\TelegramBot;
use App\Models\Report;
use App\Models\User;
use App\Models\UserWaitAccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TelegramHelpers
{
    /**
     * Возвращем стартовый набор кнопок для пользователя с доступом
     * @return array
     */
    public static function startKeyboard()
    {
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Отправить отчёт за сегодня'],
                ],
                [
                    ['text' => 'Включить уведомления'],
                ],
                [
                    ['text' => 'Выключить уведомления'],
                ],
                [
                    ['text' => 'Отменить'],
                ]
            ],
            "one_time_keyboard" => true, // можно заменить на FALSE, клавиатура скроется после нажатия кнопки автоматически при True
            "resize_keyboard" => true, // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
        ];
        return $keyboard;
    }

    /**
     * Возращает кнопку "Отменить"
     * @return array
     */
    public static function exitKeyboard()
    {
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Отменить']
                ]
            ],
            "one_time_keyboard" => true, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
            "resize_keyboard" => true, // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
        ];
        return $keyboard;
    }

    /**
     * Статический метод для отправки сообщения в бот из любого контроллера
     * @param $message
     * @param $chat_id
     * @param null $keyboard
     */
    public static function sendMessageBot($message, $chat_id, $keyboard = null)
    {
        $telegram_bot = new TelegramBot(null, $chat_id);
        $telegram_bot->sendMessage($message, $keyboard);
    }

    /**
     * Статический метод для отправки документа в бот из любого контроллера
     * @param $file_url
     * @param null $chat_id
     * @param null $keyboard
     */
    public static function sendDocument($file_url, $file_name, $chat_id = null)
    {
        $telegram_bot = new TelegramBot(null, $chat_id);
        return $telegram_bot->sendDocumentCustom($file_url, $file_name);
    }

    /**
     * Проверка не отменяет ли пользователь все действия
     * @param $text
     * @return bool
     */
    public static function checkExit($text)
    {
        if ($text == '/exit' || $text == 'Отменить') {
            return true;
        }
        return false;
    }

    /**
     * Проверка существования сессии телеграмма пользователя
     * @param $chat_id
     * @return bool
     */
    public static function checkSession(&$chat_id)
    {
        if (Storage::disk('public')->exists('telegram-sessions/chat-sessions/' . $chat_id)) {
            return true;
        }
        return false;
    }

    /**
     * Проверка кнопки и возвращаем значение
     * @param $chat_id
     * @return array|false ['report-today' => 'true'] | ['report-date' => '2021-02-25']
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function checkButton(&$chat_id)
    {
        if (Storage::disk('public')->exists('telegram-sessions/chat-sessions/' . $chat_id)) {

            $button = Storage::disk('public')->get('telegram-sessions/chat-sessions/' . $chat_id);
            return json_decode(trim($button), true);
        }

        return false;
    }

    /**
     * Проверка пользователя на доступ
     * @param $chat_id
     * @return bool
     */
    public static function checkInviteUser(&$chat_id): bool
    {
        $user = User::where('tg_chat_id', $chat_id)->first();

        if (!$user) {
            return false;
        }
        return true;
    }

    /**
     * Проверям был ли приглашён пользователь в бота
     * @param TelegramBot $telegram_bot
     * @return bool
     */
    public static function checkInvite(TelegramBot $telegram_bot) : bool
    {
        $chat_id = $telegram_bot->getChatId();
        $text    = $telegram_bot->getMessageText();

        $check_invite = self::checkInviteUser($chat_id);

        if (!$check_invite) {

            $access = UserWaitAccess::where('tg_chat_id', $chat_id)->first();

            if ($text == 'Запросить доступ') {

                /* Проверяем был ли запрос, иначе создаём запрос в БД */

                if ($access) {
                    $telegram_bot->sendMessage('Вы уже запрашивали доступ, ожидайте подтверждения');
                } else {
                    UserWaitAccess::create([
                        'tg_username'   => $telegram_bot->getUserName(),
                        'tg_title_name' => $telegram_bot->getFirstName(),
                        'tg_chat_id'    => $telegram_bot->getChatId(),
                    ]);

                    $telegram_bot->sendMessage('Вы запросили доступ, ожидайте подтверждения проект-менеджера');
                }

            } else {

                if (!$access) {
                    $keyboard = self::requestAccessKeyboard();
                    $telegram_bot->sendMessage('Вы не были приглашены, обратитесь к Project Manager или нажмите кнопку "Запросить доступ"', $keyboard);
                } else {
                    $telegram_bot->sendMessage('Вы уже запрашивали доступ, ожидайте подтверждения');
                }
            }

            return false;
        } else {

            return true;
        }
    }

    /**
     * Распознаем команду (кнопку)
     * @param TelegramBot $telegram_bot
     * @return false|string
     */
    public static function validatorCommand(TelegramBot $telegram_bot)
    {
        switch ($telegram_bot->getMessageText()) {
            case "Отправить отчёт за сегодня":
                $command = '/report-today';
                break;
            //case "Отправить отчёт за другую дату":
            //    $command = '/report-date';
            //    break;
            case "Включить уведомления":
                $command = '/enable-notify';
                break;
              case "Выключить уведомления":
                  $command = '/turn-off-notify';
                  break;
            case "Отменить":
                $command = '/exit';
                break;
            default:
                $command = "/other";
                break;
        }
        return $command;
    }

    /**
     * Возврат клавиатуры с запросом подписки
     * @return array
     */
    public static function requestAccessKeyboard()
    {
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => 'Запросить доступ']
                ]
            ],
            "one_time_keyboard" => true, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
            "resize_keyboard" => true, // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
        ];

        return $keyboard;
    }

    /**
     * По полученной команде выполняем необходимые действия
     * @param $command
     */
    public static function routerCommand($command, TelegramBot $telegram_bot)
    {
        switch ($command) {

            case '/report-today':
                $keyboard = TelegramHelpers::exitKeyboard();
                $telegram_bot->sendMessage('Введите содержание сегодняшнего отчёта... При необходимости вы сможете дополнить отчёт, таким же способом', $keyboard);

                /* Записываем в файл сессии юзера информацию о кнопке */
                $write_button = ['report-today' => 'true'];
                TelegramHelpers::writeInDocument($write_button, $telegram_bot);
                break;
            //case '/report-date':
            //    $keyboard = TelegramHelpers::exitKeyboard();
            //    $telegram_bot->sendMessage('Введите нужную дату в формате: "2021-02-25"', $keyboard);

            //    $write_button = ['report-date' => ''];
            //    TelegramHelpers::writeInDocument($write_button, $telegram_bot);
            //    break;
            case '/enable-notify':
                User::where('tg_chat_id', $telegram_bot->getChatId())->update(['notification' => 1]);
                $telegram_bot->sendMessage('Уведомление включено');
                break;
            case '/turn-off-notify':
                User::where('tg_chat_id', $telegram_bot->getChatId())->update(['notification' => 0]);
                $telegram_bot->sendMessage('Уведомление выключено');
                break;
            case '/not-invite':
                /* Записываем в файл сессии юзера информацию о кнопке */
                $write_button = ['report-today' => 'true'];
                TelegramHelpers::writeInDocument($write_button, $telegram_bot);
                break;
            case '/other':
                $keyboard = TelegramHelpers::startKeyboard();
                $telegram_bot->sendMessage('Сделайте выбор:', $keyboard);
                break;
        }
    }

    /**
     * Запись в документ массив
     * @param $write_button ['button' => value]
     * @param TelegramBot $telegram_bot
     */
    public static function writeInDocument($write_button, TelegramBot $telegram_bot)
    {
        $chat_id = $telegram_bot->getChatId();

        Storage::disk('public')->put('telegram-sessions/chat-sessions/' . $chat_id, json_encode($write_button));
    }

    /**
     * По имени нажатой кнопки совершаем соответствующие действия
     * @param $button
     * @param TelegramBot $telegram_bot
     * @return false
     */
    public static function routerButton($button, TelegramBot $telegram_bot)
    {
        $name_button = key($button);
        $text    = $telegram_bot->getMessageText();
        $chat_id = $telegram_bot->getChatId();

        switch ($name_button) {
            case 'report-today':

                /* Сохраняем сегодняшний отчет или дописываем сегодняшний отчет */
                $user = User::where('tg_chat_id', $chat_id)->first();

                $isset_report = $user->reports()->whereDate('created_at', Carbon::today()->toDateString())->first();

                if ($isset_report) {

                    $isset_report->report = $isset_report->report . "\n" . $text;
                    $isset_report->save();

                } else {
                    Report::create(['user_id' => $user->id, 'report' => $text]);
                }

                $keyboard = self::startKeyboard();

                $telegram_bot->sendMessage('Отчёт сохранён. Дополнить отчёт можно таким же способом', $keyboard);
                Storage::disk('public')->delete('telegram-sessions/chat-sessions/' . $chat_id);

                break;
            case 'report-date':
                break;
                /* Если в файле ещё не записана дата */

                if (empty($button['report-date'])) {

                    /* Проверка на формат даты */

                    $date = $text;
                    $format_date = self::validatorDate($date);

                    $keyboard = TelegramHelpers::exitKeyboard();

                    if ($format_date) {
                        $telegram_bot->sendMessage('Введите содержание просроченого отчёта...', $keyboard);

                        /* Записываем в файл сессии юзера информацию о кнопке */
                        $write_button = ['report-date' => $date];
                        self::writeInDocument($write_button, $telegram_bot);

                    } else {
                        $telegram_bot->sendMessage('Неверный формат даты отчёта, пример: 2021-03-30', $keyboard);
                    }
                    return false;
                } else {

                    /* Сохраняем отчет или дописываем существующий отчет */

                    $user = User::where('tg_chat_id', $chat_id)->first();

                    $date = $button['report-date'];

                    if (!empty($date)) {

                        $format_date = self::validatorDate($date);

                        if ($format_date) {

                            $isset_report = $user->whereDate('created_at', $date)->first();

                            if ($isset_report) {

                                $isset_report->report = $isset_report->report . "\n" . $text;
                                $isset_report->save();

                            } else {
                                Report::insert(['user_id' => $user->id, 'report' => $text, 'created_at' => $date, 'updated_at' => $date]);
                            }

                            $keyboard = self::startKeyboard();

                            $telegram_bot->sendMessage('Отчёт за ' . $date . ' сохранён. Дополнить отчёт можно таким же способом', $keyboard);

                            Storage::disk('public')->delete('telegram-sessions/chat-sessions/' . $chat_id);
                        }
                    }
                }
            break;
        }
    }

    /**
     * Проверка формата даты
     * @param $date
     * @return bool
     */
    public static function validatorDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
