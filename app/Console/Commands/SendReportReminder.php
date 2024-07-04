<?php

namespace App\Console\Commands;

use App\Helpers\libraries\TelegramBot;
use App\Models\Report;
use App\Models\User;
use Illuminate\Console\Command;

class SendReportReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send-reminder';

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
        $reportsId = Report::select('user_id')->getReportsToday()->get()->pluck('user_id')->toArray();

        $usersToSendNotifyTo = User::where('notification', 1)->whereNotIn('id', $reportsId)->get();

        foreach ($usersToSendNotifyTo as $user) {
            $this->telegram_bot = new TelegramBot(null, $user->tg_chat_id);
            $this->telegram_bot->sendMessage('ОТЧЕТ!');
        }
    }
}
