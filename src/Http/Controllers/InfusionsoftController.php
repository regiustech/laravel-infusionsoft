<?php
namespace Binfotech\Infusionsoft\Http\Controllers;
use App\Http\Controllers\Controller;
use Infusionsoft\InfusionsoftException;
use Binfotech\Infusionsoft\Infusionsoft;

class InfusionsoftController extends Controller
{
    public function auth($account = false){
        $infusionsoft = new Infusionsoft($account);
        return redirect($infusionsoft->getAuthorizationUrl());
    }
    public function callback(\Illuminate\Http\Request $request){
        $infusionsoft = config('infusionsoft.multi') ? new Infusionsoft($request->account) : new Infusionsoft();
        $res = $infusionsoft->data('xml')->query('Contact',1,0,['Id' => '*'],['Id'],'Id',true);
        if(!is_array($res)){
            throw new InfusionsoftException('There was an issue connecting to Infusionsoft using your access code');
        }
        return 'Token set successfully. You may now use the Infusionsoft API.';
    }
}