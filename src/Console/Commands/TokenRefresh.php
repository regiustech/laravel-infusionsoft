<?php
namespace Binfotech\Infusionsoft\Console\Commands;
use Illuminate\Console\Command;

class TokenRefresh extends Command
{
    protected $signature = 'infusionsoft:token-refresh';
    protected $description = 'Refresh Infusionsoft access token(s)';
    private $client_id;
    private $client_secret;
    private $token_name;
    private $store;
    public function handle(){
        $this->store = config('infusionsoft.cache');
        if(config('infusionsoft.multi')){
            $infusionsoft_accounts = json_decode(trim(stripslashes(config('infusionsoft.accounts'))), true);
            foreach($infusionsoft_accounts as $account){
                $key = array_key_first($account);
                $this->client_id = $account[$key]['client_id'];
                $this->client_secret = $account[$key]['client_secret'];
                $this->token_name = sprintf('%s.%s', config('infusionsoft.token_name'), $key);
                $this->refreshAccessToken();
            }
        }else{
            $this->client_id = config('infusionsoft.client_id');
            $this->client_secret = config('infusionsoft.client_secret');
            $this->token_name = sprintf('%s.default', config('infusionsoft.token_name'));
            $this->refreshAccessToken();
        }
    }
    public function refreshAccessToken(){
        $inf = new \Infusionsoft\Infusionsoft([
            'clientId' => $this->client_id,
            'clientSecret' => $this->client_secret,
        ]);
        $token = cache()->store($this->store)->get($this->token_name,false);
        $token = new \Infusionsoft\Token(unserialize($token));
        $token->setEndOfLife($token->getEndOfLife() - time());
        $inf->setToken($token);
        $token = $inf->refreshAccessToken();
        $this->storeAccessToken($token);
    }
    public function storeAccessToken($token){
        $extra = $token->getExtraInfo();
        cache()->store($this->store)->forever($this->token_name,serialize([
            'access_token' => $token->getAccessToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_in' => $token->getEndOfLife(),
            'token_type' => $extra['token_type'],
            'scope' => $extra['scope']
        ]));
    }
}