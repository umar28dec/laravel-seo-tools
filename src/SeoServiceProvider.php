<?php

namespace SEO;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use SEO\Models\MetaTag;
use SEO\Models\Page;
use SEO\Models\PageImage;
use SEO\Models\Setting;
use App\Policies\Seo\ImagePolicy;
use App\Policies\Seo\MetaTagPolicy;
use App\Policies\Seo\PagePolicy;
use App\Policies\Seo\SettingPolicy;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * The policy mPermitings for the Permitlication.
     *
     * @var array
     */
    protected $policies = [
        //'Model' => 'Policies\ModelPolicy',
        Setting::class => SettingPolicy::class,
        Page::class => PagePolicy::class,
        MetaTag::class => MetaTagPolicy::class,
        PageImage::class => ImagePolicy::class
    ];

    /**
     *
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seo');
        $this->publishes([
            __DIR__ . '/../config/seo.php' => config_path('seo.php'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/seo'),
        ]);
        $this->publishes([
            __DIR__ . '/Policies' => base_path('app/Policies/Seo')
        ], 'seo-policies');
    }

    /**
     *
     */
    public function register()
    {
        $this->registerPolicies();
        $this->mergeConfigFrom(
            __DIR__ . '/../config/seo.php', 'seo'
        );
        Blade::directive('seoForm', function ($model) {
            return "<?php echo \SEO\Seo::form($model); ?>";
        });
        Blade::directive('seoTags', function ($model) {
            return "<?php print((new \SEO\Seo())->tags()); ?>";
        });
        Event::listen(['eloquent.saved: *', 'eloquent.created: *'], function ($name, $models) {
            foreach ($models as $model) {
                if (in_array(get_class($model), config('seo.models'))) {

                }
            }

        });
    }

    /**
     * Register the Permitlication's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * To register seo as first level command. E.g. seo:model
     *
     * @return array
     */
    public function provides()
    {
        return ['seo'];
    }

}