# payment-service
Laravel implemented payment service 

This service is actually the payment service that I use for my Laravel project.
All you need to do is copy the ``payment`` folder and paste it in App directory in your Laravel project.

## How does it work?

If you wanna see the example ensure that you checked the ``example`` directory in this repository, It gives you an example of usage in ``PaymentController``.
<br>
The pattern we are using is [Strategy](https://refactoring.guru/design-patterns/strategy) that is one of the behavioral patterns. <br>
First we need to define our interfaces to know what actually we will face in this service, We all know that every single provider has a ``pay`` method becuase it needs to be paied but some of them has ``verify`` method because all the payment are not verifiable, Like offline transactions. <br>
So we have to define two different interfaces called ``Payable`` and ``Verifiable`` (These interfaces are in ``Payment/Contracts``).

#### Payable interface
```php
namespace App\Services\Payment\Contracts;

interface PayableInterface
{
    public function pay();
}
```

#### Verifiable interface
```php
namespace App\Services\Payment\Contracts;

interface VerifiableInterface
{
    public function verify();
}
```
So every provider that wants to be added to this service has to implement at least the Payable interface, Like:
```php
namespace App\Services\Payment\Providers;

use App\Services\Payment\Contracts\PayableInterface;
use App\Services\Payment\Contracts\VerifaibleInterface;

class ZarinpalProvider implements PayableInterface, VerifaibleInterface
{
    public function pay()
    {
        // TODO: Implement pay() method.
    }
    
    public function verify()
    {
        // TODO: Implement pay() method.
    }
```

### Let's find out How payment service class works

Let me show you the actual code then we can discuss about it.

```php
namespace App\Services\Payment;

use App\Services\Payment\Contracts\RequestInterface;
use App\Services\Payment\Exceptions\ProviderNotFoundException;

class PaymentService 
{
    public const IDPAY = 'IDPayProvider';
    public const ZARINPAL = 'ZarinpalProvider';

    public function __construct(private string $providerName, 
                                private RequestInterface $request)
    {
    }

    public function pay()
    {
        return $this->findProvider()->pay();
    }

    public function verify()
    {
        return $this->findProvider()->verify();
    }


    private function findProvider()
    {
        $className = 'App\\Services\\Payment\\Providers\\' . $this->providerName;
        
        if(!class_exists($className))
        {
            throw new ProviderNotFoundException('درگاه پرداخت انتخاب شده پیدا نشد');
        }

        return new $className($this->request);
    }
}

```

If you want to use Payment you have to start from ``PaymentService`` class. In This class we defined our different provider names as constant, Like ``IDPAY`` or ``ZarrinPal``. <br>
In the construct of class we have two parameters, First one if ``$dataRequest`` and the second one is the provider type. <br>
#### What is ``$dataRequest`` anyway?
In the example of this class you will see:
```php


$idPayRequest = new IDPayRequest([
                'amount' => $productsPrice,
                'user' => $user,
                'orderId' => $refCode,
                'apiKey' => config('services.gateways.id_pay.api_key'),
            ]);

            $paymentService = new PaymentService(PaymentService::IDPAY, $idPayRequest);
```
If you noticed the constructor the first paramter is type of ``IDPayRequest`` , This class has the necessary data that we need to use in every single provider, Like ``amount``, the actual ``user``, ``user's bank account``, etc to pay or verify!<br>

<hr>

The ``findProvider`` method in the ``PaymentService`` class returns the object of the provider that you passed in the second parameter of the constructor. <br>
As we defined our interfaces we are sure that both of them has at least the ``Pay`` method, So in the ``sendToProvider`` method we call the ``pay`` method on the object that ``sendToProvider`` returns to us and we pass the ``$dataReqeust`` as well, Ultimately the user will be redirected to payment page. <br>

### What happens when the user come back from payment page?
In this case users get back to ``verify`` method from payment page to the route that you given as ``callback`` route. <br>

