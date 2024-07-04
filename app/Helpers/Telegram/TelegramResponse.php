<?php

namespace App\Helpers\Telegram;

use App\Helpers\Telegram\Bots\BotFactory;

class TelegramResponse
{
    /** @var BotsManager class Bot Factory. */
    private BotsManager $botsManager;

    public function __construct()
    {
        $this->botsManager = new BotsManager();
    }

    /**
     * Redirects the response to the desired bot class
     *
     * @param string $botName - bot name according to the configuration
     * @param $response - response from telegram api
     *
     * @return void
     */
    public function handler(string $botName, $response): void
    {
        $botConfig = $this->botsManager->getBotConfig($botName);

        $bot = (new BotFactory())->make(data_get($botConfig, 'class'));

        $bot->webhook($response);
    }

}