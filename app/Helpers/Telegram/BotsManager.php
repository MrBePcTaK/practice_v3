<?php

namespace App\Helpers\Telegram;

class BotsManager
{
    private ?ContainerInterface $container = null;

    /** @var array The active bot instances. */
    private array $bots = [];

    private array $config;

    public function __construct()
    {
        $this->config = config('bots');
    }

    /**
     * Get the configuration for a bot.
     *
     * @param string|null $name
     *
     * @return null[]|string[]
     */
    public function getBotConfig(string $name = null): array
    {
        $name ??= $this->getDefaultBotName();

        $bots = collect($this->getConfig('bots'));

        $config = $bots->get($name);

        if (!$config) {
            //TODO:: add error handler
        }

        return $config + ['bot' => $name];
    }

    /**
     * Get the default bot name.
     *
     * @return string|null
     */
    private function getDefaultBotName(): ?string
    {
        return $this->getConfig('default');
    }

    /**
     * Get the specified configuration value for Telegram.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}