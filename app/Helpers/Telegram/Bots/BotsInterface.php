<?php

namespace App\Helpers\Telegram\Bots;

interface BotsInterface
{
    public function webhook($response);
}