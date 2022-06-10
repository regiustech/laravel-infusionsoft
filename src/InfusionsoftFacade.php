<?php
namespace Binfotech\Infusionsoft;
use Illuminate\Support\Facades\Facade;

class InfusionsoftFacade extends Facade
{
    protected static function getFacadeAccessor(){
        return 'infusionsoft';
    }
}