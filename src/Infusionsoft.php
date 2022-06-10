<?php
namespace Binfotech\Infusionsoft;
use Infusionsoft\Infusionsoft as Inf;
use Binfotech\Infusionsoft\Api\Rest\LocaleService;
use Binfotech\Infusionsoft\Api\Rest\AffiliateService;
use Binfotech\Infusionsoft\Api\Rest\ContactService;
use Binfotech\Infusionsoft\Api\Rest\CompanyService;

class Infusionsoft extends Inf
{
    protected $token_name;
    protected $authorization_code;
    protected $store;

    public function __construct($account = false){
        $is_multi = is_string($account);
        $this->store = config('infusionsoft.cache');
        if($is_multi){
            $this->setTokenName(sprintf('%s.%s',config('infusionsoft.token_name'),$account));
        }else{
            $this->setTokenName(sprintf('%s.default',config('infusionsoft.token_name')));
        }
        if(config('infusionsoft.multi') && $is_multi){
            $infusionsoft_accounts = json_decode(trim(stripslashes(config('infusionsoft.accounts'))),true);
            $infusionsoft_account = current(
                array_filter($infusionsoft_accounts,function($infusionsoft_account) use ($account){
                    return $account === array_key_first($infusionsoft_account);
                })
            );
            if(!$infusionsoft_account){
                throw new \Infusionsoft\InfusionsoftException('Infusionsoft account could not be found in config using multi',1);
            }
            $client_id = $infusionsoft_account[$account]['client_id'];
            $client_secret = $infusionsoft_account[$account]['client_secret'];
            $redirect_uri = $infusionsoft_account[$account]['redirect_uri'];
        }elseif(config('infusionsoft.multi')){
            throw new \Infusionsoft\InfusionsoftException('Infusionsoft is set up for multi, but no account was set',1);
        }else{
            $client_id = config('infusionsoft.client_id');
            $client_secret = config('infusionsoft.client_secret');
            $redirect_uri = config('infusionsoft.redirect_uri');
        }
        if(empty($client_id)){
            throw new \Infusionsoft\InfusionsoftException('Infusionsoft Client ID not present', 1);
        }
        if(empty($client_secret)){
            throw new \Infusionsoft\InfusionsoftException('Infusionsoft Client Secret not present', 1);
        }
        $this->setClientId($client_id);
        $this->setClientSecret($client_secret);
        $this->setRedirectUri(url($redirect_uri));
        if(config('infusionsoft.debug')){
            $this->setDebug(true);
        }
        $token = cache()->store($this->store)->get($this->token_name,false);
        if($token){
            $token = new \Infusionsoft\Token(unserialize($token));
            $token->setEndOfLife($token->getEndOfLife() - time());
            $this->setToken($token);
            if($token->isExpired()){
                $token = $this->refreshAccessToken();
                $this->setToken($token);
                $this->storeAccessToken();
            }
        }elseif(request()->has('code')){
            $this->requestAccessToken(request()->get('code'));
            $this->storeAccessToken();
        }else{
            return redirect($this->getAuthorizationUrl());
        }
    }
    public function setAuthorizationCode($authorization_code){
        $this->authorization_code = $authorization_code;
        return $this;
    }
    public function setTokenName($name){
        $this->token_name = $name;
        return $this;
    }
    public function storeAccessToken(){
        $token = $this->getToken();
        $extra = $token->getExtraInfo();
        cache()->store($this->store)->forever($this->token_name,serialize([
            'access_token' => $token->getAccessToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_in' => $token->getEndOfLife(),
            'token_type' => $extra['token_type'],
            'scope' => $extra['scope']
        ]));
    }
    public function affiliates($api = 'rest'){
        if($api == 'xml'){
            return $this->getApi('AffiliateService');
        }
        return $this->getRestApi(AffiliateService::class);
    }
    public function contacts($api = 'rest'){
        if($api == 'xml'){
            return $this->getApi('ContactService');
        }
        return $this->getRestApi(ContactService::class);
    }
    public function companies($api = 'rest'){
        if($api == 'xml'){
            return $this->getApi('CompanyService');
        }
        return $this->getRestApi(CompanyService::class);
    }
    public function locales(){
        return $this->getRestApi(LocaleService::class);
    }
    public function getRestApi($class){
        if(!class_exists($class)){
            $class = '\Infusionsoft\Api\Rest\\'.$class;
        }
        return new $class($this);
    }
    public function try(callable $callback,int $max_tries = 5,int $sleep = 2){
        $tries = 0;
        do{
            try{
                return $callback($this,$tries);
            }catch(\Throwable $th){
                sleep($sleep);
                $tries++;
                if($tries === $max_tries){
                    throw $th;
                }
            }
        }while($tries < $max_tries);
    }
}