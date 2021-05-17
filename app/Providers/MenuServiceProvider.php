<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
            $event->menu->addIn('notifications', [
                "key" => "notification_type_1",
                "text" => Notification::TYPES[Notification::TYPE_CANCEL_SUBSCRIPTION],
                "url" => "notifications?type=1&processed=0",
                'label'       => Notification::whereType(Notification::TYPE_CANCEL_SUBSCRIPTION)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);
            $event->menu->addIn('notifications', [
                "key" => "notification_type_2",
                "text" => Notification::TYPES[Notification::TYPE_SUBSCRIPTION_ERRORS],
                "url" => "notifications?type=2&processed=0",
                'label'       => Notification::whereType(Notification::TYPE_SUBSCRIPTION_ERRORS)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);
            $event->menu->addIn('notifications', [
                "key" => "notification_type_7",
                "text" => Notification::TYPES[Notification::WAITING_PAYMENT_CP],
                "url" => "notifications?type=7&processed=0",
                'label'       => Notification::whereType(Notification::WAITING_PAYMENT_CP)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);
            $event->menu->addIn('notifications', [
                "key" => "notification_type_4",
                "classes" => 'long-title',
                "text" => Notification::TYPES[Notification::TYPE_ENDED_SUBSCRIPTIONS_DT],
                "url" => "notifications?type=4&processed=0",
                'label'       => Notification::whereType(Notification::TYPE_ENDED_SUBSCRIPTIONS_DT)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);
            $event->menu->addIn('notifications', [
                "key" => "notification_type_5",
                'classes' => 'long-title',
                "text" => Notification::TYPES[Notification::TYPE_ENDED_SUBSCRIPTIONS_DT_3],
                "url" => "notifications?type=5&processed=0",
                'label'       => Notification::whereType(Notification::TYPE_ENDED_SUBSCRIPTIONS_DT_3)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);
            $event->menu->addIn('notifications', [
                "key" => "notification_type_6",
                "text" => Notification::TYPES[Notification::TYPE_ENDED_TRIAL_PERIOD],
                "url" => "notifications?type=6&processed=0",
                'label'       => Notification::whereType(Notification::TYPE_ENDED_TRIAL_PERIOD)->whereProcessed(false)->count(),
                'label_color' => 'success',
            ]);

            $event->menu->addIn('statistics', [
                "key" => "statistic_type_1",
                "text" => 'Количественные',
                "url" => "statistics/quantitative",
            ]);

            $event->menu->addIn('statistics', [
                "key" => "statistic_type_2",
                "text" => 'Финансовые',
                "url" => "statistics/financial",
            ]);

            $event->menu->addIn('statistics', [
                "key" => "statistic_type_3",
                "text" => 'Итоговые',
                "url" => "statistics/total",
            ]);
        });
    }

    private function registerServices()
    {
    }
}
