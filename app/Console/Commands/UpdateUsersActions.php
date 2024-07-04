<?php

namespace App\Console\Commands;

use App\Helpers\TelegramHelpers;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUsersActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update-actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::active()->get();

        foreach ($users as $user) {
            $keyboard = TelegramHelpers::startKeyboard();
            TelegramHelpers::sendMessageBot('Что-то обновилось.', $user->tg_chat_id, $keyboard);
        }
    }
}
