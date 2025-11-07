<?php

namespace App\Providers;

use App\Channels\WebSocketChannel;
use App\Events\OrderTransactionEvent;
use App\Listeners\AdminInformTransaction;
use App\Listeners\CustomerInformTransaction;
use App\Listeners\HandleOrderStatus;
use App\Listeners\HandleOrderStock;
use App\Listeners\Initialize;
use App\Actions\UserActions;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserActions::class, fn() => new UserActions);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Event::listen(OrderTransactionEvent::class, [
            // needs Initialize to make the listeners work
            Initialize::class,
            HandleOrderStock::class,
            HandleOrderStatus::class,
            CustomerInformTransaction::class,
            AdminInformTransaction::class,
        ]);

        app(ChannelManager::class)->extend('websocket', function () {
            return new WebSocketChannel();
        });
    }
}