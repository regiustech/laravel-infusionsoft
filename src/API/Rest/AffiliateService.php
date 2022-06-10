<?php
namespace Binfotech\Infusionsoft\Api\Rest;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Api\Rest\RestModel;
use Infusionsoft\Api\Rest\Traits\CannotSave;
use Infusionsoft\Api\Rest\Traits\CannotSync;
use Infusionsoft\Api\Rest\Traits\CannotDelete;

class AffiliateService extends RestModel
{
    use CannotSync;
    use CannotSave;
    use CannotDelete;

    public $full_url = 'https://api.infusionsoft.com/crm/rest/v1/affiliates';
    public $return_key = 'affiliates';
    public function __construct(Infusionsoft $client){
        parent::__construct($client);
    }
    public function clawbacks($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl($this->id.'/clawbacks'),$params);
    }
    public function payments($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl($this->id.'/payments'),$params);
    }
    public function commissions($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl('/commissions'),$params);
    }
    public function programs($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl('/programs'),$params);
    }
    public function redirectLinks($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl('/redirectlinks'),$params);
    }
    public function summaries($params = []){
        return $this->client->restfulRequest('get',$this->getFullUrl('/summaries'),$params);
    }
}