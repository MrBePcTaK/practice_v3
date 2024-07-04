<?php

namespace App\Helpers\Telegram\Bots;

interface BotFactoryContract
{
    public function make($class);
}