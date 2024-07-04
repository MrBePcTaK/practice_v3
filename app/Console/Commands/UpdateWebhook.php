<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update {bot=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes telegram webhook and set new based on .env WEBHOOK_URL and HOST_URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function updateWebhook($url, $token): void
    {
        Http::get('https://api.telegram.org/bot' . $token . '/deleteWebhook');

        $response = Http::post('https://api.telegram.org/bot' . $token . '/setWebhook', [
            'url' => $url
        ]);

        if ($response->status() == 200) {
            $this->line('Webhook set on ' . $url);
        } else {
            $this->line('Error ' . $response->status() . ' with url ' . $url);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $param = $this->argument('bot');

        $botNames = array_keys(config('bots.bots'));
        if ($param == 'all') {
            foreach (config('bots.bots') as $bot) {
                if ($bot['token'] != '') {
                    $this->updateWebhook($bot['webhook_url'], $bot['token']);
                }
            }
        } elseif (in_array($param, $botNames)) {
            $bot = config('bots.bots.' . $param);
            if($bot['token'] == '') {
                $this->line($param . ' bot\'s token empty');
            } else {
                $this->updateWebhook($bot['webhook_url'], $bot['token']);
            }
        } else {
            $this->line($param . ' bot name does not exist');
        }
    }
}
