<?php

namespace Platform\Main\Providers;

use Platform\Main\Models\Main;
use Illuminate\Support\ServiceProvider;
use Platform\Main\Repositories\Caches\MainCacheDecorator;
use Platform\Main\Repositories\Eloquent\MainRepository;
use Platform\Main\Repositories\Interfaces\MainInterface;
use Platform\Base\Supports\Helper;
use Event;
use Platform\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class MainServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(MainInterface::class, function () {
            return new MainCacheDecorator(new MainRepository(new Main));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/main')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Main::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-main',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/main::main.name',
                'icon'        => 'fa fa-list',
                'url'         => route('main.index'),
                'permissions' => ['main.index'],
            ]);
        });
    }
}
