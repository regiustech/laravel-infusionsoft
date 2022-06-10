<?php
namespace Binfotech\Infusionsoft\Api\Rest;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Api\Rest\RestModel;
use Infusionsoft\Api\Rest\Traits\CannotSync;

class CompanyService extends RestModel
{
    use CannotSync;
    public $full_url = 'https://api.infusionsoft.com/crm/rest/v1/companies';
    protected $updateVerb = 'patch';
    public $return_key = 'companies';
    public function __construct(Infusionsoft $client){
        parent::__construct($client);
    }
    public function create(array $attributes = [],$dupCheck = false){
        $this->mock($attributes);
        if($dupCheck){
            $data = $this->client->restfulRequest($this->updateVerb,$this->getFullUrl($dupCheck),(array)$this->toArray());
            $this->fill($data);
        }else{
            $this->save();
        }
        return $this;
    }
}