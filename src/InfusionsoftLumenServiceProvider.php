<?php
namespace Binfotech\Infusionsoft;
use Illuminate\Support\ServiceProvider;

class InfusionsoftLumenServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->singleton('infusionsoft',function($app){
            return new Infusionsoft($app);
        });
        $this->registerRoutes();
        $this->app->make('Binfotech\Infusionsoft\Http\Controllers\InfusionsoftController');
    }
    private function registerRoutes(){
        $this->app->group(['prefix' => 'infusionsoft','namespace' => 'Binfotech\Infusionsoft\Http\Controllers'],function($app){
            $app->get('auth/callback','InfusionsoftController@callback');
            $app->get('auth','InfusionsoftController@auth');
        });
    }
}