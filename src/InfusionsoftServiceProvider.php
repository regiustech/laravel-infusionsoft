<?php
namespace Binfotech\Infusionsoft;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Binfotech\Infusionsoft\Console\Commands\TokenRefresh;

class InfusionsoftServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->registerRoutes();
        $this->publishes([__DIR__ . '/../config/config.php' => config_path('infusionsoft.php')],'config');
        if($this->app->runningInConsole()){
            $this->commands([TokenRefresh::class]);
        }
    }
    public function register(){
        $this->app->singleton('infusionsoft',function($app){
            return new Infusionsoft($app);
        });
        $this->app->alias('infusionsoft',Infusionsoft::class);
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php','infusionsoft');
    }
    private function registerRoutes(){
        Route::group(['prefix' => 'infusionsoft','namespace' => 'Binfotech\Infusionsoft\Http\Controllers','middleware' => 'web'],function(){
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
    public function provides(){
        return [];
    }
}