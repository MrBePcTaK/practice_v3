<?php

namespace App\Helpers\Telegram\FoodOrders;

use App\Helpers\libraries\TelegramBot;
use App\Helpers\Telegram\Bots\BotsInterface;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FoodOrders implements BotsInterface
{
    private TelegramBot $telegramBot;

    private function getWebAppData($response)
    {
        if (!empty($response)) {
            if (is_array($response)) {
                if (isset($response['message']['web_app_data'])) {
                    return $response['message']['web_app_data'];
                }
            }
        }
        return false;
    }

    public function webhook($response)
    {
        try {
            Log::debug($response);

            $this->telegramBot = new TelegramBot($response);
            $menu = new Menu();
            $chat_id = $this->telegramBot->getChatId();
            $text = $this->telegramBot->getMessageText();
            $webAppData = $this->getWebAppData($response);

            if ($text) {
                if ($text == '/start') {
                    $keyboard = [
                        'keyboard' => [
                            [
                                [
                                    'text' => '📦 Order Food',
                                    'web_app' => [
                                        'url' => config('bots.host_url') . '/menu?id=' . $chat_id,
                                    ],
                                ]
                            ]
                        ],
                        'resize_keyboard' => true,
                    ];
                    $this->telegramBot->sendButton('Откройте меню на кнопку', $keyboard);
                } elseif(str_contains($text, 'Салаты')) {
                    $status = $menu->parseMenu($text);
                    if($status) {
                        $this->telegramBot->sendMessage('parsed');
                    } else {
                        $this->telegramBot->sendMessage('error');
                    }
                } else {
                    $this->telegramBot->sendMessage($text);
                }
            } elseif ($webAppData) {
                $data = json_decode($webAppData['data'], true);

                if(empty($data)) {
                    $this->telegramBot->sendMessage("Ваша корзина была пуста при отправке заказа.\nЗаказ не был сформирован.");
                } else {
                    $authorId = $response['message']['from']['id'];
                    $userId = User::query()
                        ->select('id')
                        ->where('tg_chat_id', $authorId)
                        ->first()->id;

                    $cost = 0;
                    foreach ($data as $item) {
                        $items[$item['id']] = $item['count'];
                        $cost += Product::query()
                                ->select('price')
                                ->where('id', $item['id'])
                                ->first()->price * $item['count'];
                    }

                    $order = Order::query()->create([
                        'user_id' => $userId,
                        'cost' => $cost,
                        'date' => now(),
                    ]);
                    $products = Product::query()->find(array_keys($items));

                    foreach ($products as $product) {
                        $order->products()->attach($product, ['count' => $items[$product->id]]);
                    }

                    $message = "Ваш заказ успешно принят (" . date('Y-m-d', strtotime($order->date)) . ")\n";
                    foreach ($products as $product) {
                        $message = $message . "\n" . 'Наименование: ' . $product->name;
                        $message = $message . "\n" . 'Количество: ' . $items[$product->id] . "шт.\n";
                    }
                    $message = $message . "\nСтоимость: " . $cost . '₽';
                    $this->telegramBot->sendMessage($message);
                }
            }
        } catch (\Exception $exception) {
            $this->telegramBot->sendMessage($exception->getMessage());
            return response('Exception ' . $exception->getMessage(), 200);
        }
    }
}
