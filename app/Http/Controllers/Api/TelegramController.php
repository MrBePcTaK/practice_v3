<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Telegram\TelegramResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    /** @var TelegramResponse class to handle the response. */
    private TelegramResponse $telegramResponse;

    public function __construct()
    {
        $this->telegramResponse = new TelegramResponse();
    }

    /**
     * Central handler for all webhooks from Telegram
     *
     * @param Request $request - response from telegram api
     *
     * @return void
     */
    public function webhook(Request $request): void
    {
        try {
            $query = $request->query('bot');
            $botNames = array_keys(config('bots.bots'));
            if (!is_null($query) and in_array($query, $botNames)) {
                $botName = $query;
                $this->telegramResponse->handler($botName, $request->json()->all());
            }
        } catch (\Exception $exception) {
            //TODO:: add error handler
            Log::info('ERROR: ' . $exception->getMessage());
        }
    }
}
