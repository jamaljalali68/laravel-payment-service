<?php


namespace App\Services\Payment\Requests;

use App\Services\Payment\Contracts\RequestInterface;

class IDPayVerifyRequest implements RequestInterface
{
    private $orderId;
    private $id;
    private $apiKey;

    public function __construct(array $data)
    {

  
        $this->orderId = $data['orderId'];
        $this->id = $data['id'];
        $this->apiKey = $data['apiKey'];

    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }


}