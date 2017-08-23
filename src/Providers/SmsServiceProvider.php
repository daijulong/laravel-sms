<?php

namespace Daijulong\LaravelSms\Providers;

use Daijulong\LaravelSms\Console\SmsAgentCommand;
use Daijulong\LaravelSms\Console\SmsCreateCommand;
use Daijulong\LaravelSms\SmsSender;
use Daijulong\LaravelSms\Console\SmsTableCommand;
use Illuminate\Support\ServiceProvider;


class SmsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/sms.php' => config_path('sms.php')
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sms.php', 'sms'
        );

        if (config('app.env') != 'production') {
            $this->app->singleton('command.sms.table', function ($app) {
                return new SmsTableCommand($app['files'], $app['composer']);
            });
            $this->app->singleton('command.sms.create', function ($app) {
                return new SmsCreateCommand($app['files']);
            });
            $this->app->singleton('command.sms.agent', function ($app) {
                return new SmsAgentCommand($app['files']);
            });
            $this->commands(['command.sms.table', 'command.sms.create', 'command.sms.agent']);
        }

        $this->app->bind('sms', function ($app) {
            return new SmsSender();
        });

    }

}
