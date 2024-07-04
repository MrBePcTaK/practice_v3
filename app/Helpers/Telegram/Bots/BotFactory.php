<?php

namespace App\Helpers\Telegram\Bots;

class BotFactory implements BotFactoryContract
{
    public function make($class)
    {
        return new $class();
    }
}