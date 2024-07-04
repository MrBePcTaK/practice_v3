<?php

return [
    'bots' => [
        'reports' => [
            'token' => env('TELEGRAM_BOT_REPORTS_TOKEN'),
            'webhook_url' => env('HOST_URL') . '/api/' . env('WEBHOOK_URL') . '?bot=reports',
            'chat_id_group' => env('TELEGRAM_BOT_REPORTS_CHAT_ID_GROUP'),
            'class' => \App\Helpers\Telegram\Reports\ReportBot::class
//            'commands'    => [
////                App\Telegram\Commands\StartCommand::class,
//            ],
        ],

        'food_orders' => [
            'token' => env('TELEGRAM_BOT_FOOD_ORDERS_TOKEN'),
            'webhook_url' => env('HOST_URL') . '/api/' . env('WEBHOOK_URL') . '?bot=food_orders',
            'chat_id_group' => env('TELEGRAM_BOT_FOOD_ORDERS_CHAT_ID_GROUP'),
            'class' => \App\Helpers\Telegram\FoodOrders\FoodOrders::class
//            'commands' => ['admin', 'help', 'info'],
        ],

//        'default' => 'reports'
    ],
    'webhook_url' => env('WEBHOOK_URL'),
    'host_url' => env('HOST_URL'),
];
