Omnipay: Ceca + Bizum
===============

**Ceca NON-OFFICIAL driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements RedSys (formerly Sermepa) support for Omnipay.

Installation
------------

This is available via [Composer/Packagist](https://packagist.org/packages/eseperio/ceca-omnipay). 


Run the following command to install it:
```bash
composer require eseperio/ceca-omnipay
```


Basic Usage
-----------

In order to process a payment, you will need to create a `Gateway` with your credentials, and then create a `PurchaseRequest` and send it to the gateway.

### Creating gateway
```php
$gateway = Omnipay::create('Ceca');

$gateway->setMerchantId('your_merchant_id');
$gateway->setTerminalId('your_terminal_id');
$gateway->setAcquirerBin('your_acquirer_bin');
$gateway->setEncryptionKey('your_encryption_key');
$gateway->setTestMode(true); // skip this line to use production mode or set it to false
// IMPORTANT: this gateway use omnipay support for currency, so you must set the currenty
// using its ISO name instead of the currency code (978).
$gateway->setCurrency('EUR');
```

### Sending purchase request

```php
$response = $gateway->purchase();
    ->setTransactionId('your_transaction_id')
    ->setAmount('10.00')
    ->setDescription('your_description')
    ->setURL_OK('https://yourdomain.com/ok')
    ->setURL_NOK('https://yourdomain.com/nok');

/**
 * @var $response \Omnipay\Ceca\Message\PurchaseResponse
 */
$response = $purchaseRequest->send();
if ($response->isRedirect()) {
    $response->redirect();
} else {
    Yii::error('Payment request failed', 'omnipay');
    Yii::error($response->getMessage(), 'omnipay');
    throw new Exception($response->getMessage());
}
```

This will redirect user to the payment gateway if the request is successful. If not, an exception will be thrown.

Then, if user process payment, the gateway will redirect user to the URL_OK or URL_NOK you provided.

    IMPORTANT: URL_OK and URL_NOK are not valid for order confirmation. You must use the notification URL to confirm the order.

### Handling notification

In your application, you must create a route to handle the notification. This route will be called by the payment gateway to confirm the order.
This library implements AcceptNotification to handle this

```php
    $gateway = Omnipay::create('Ceca');
    $gateway->setMerchantId('your_merchant_id');
    [...] // same settings as before

    $notification = $gateway->acceptNotification();
    if ($notification->getTransactionStatus() == NotificationInterface::STATUS_COMPLETED) {
            // Mark your order as paid
            return;
        } else {
            throw new \Exception($notification->getMessage());
        }
```

### Simulating a notification from the gateway

In order to improve testing of local development, you can simulate a notification from the gateway by calling the `send` method of the notification request.

```php
    $gateway = Omnipay::create('Ceca');
    $gateway->setMerchantId('your_merchant_id');
    [...] // same settings as before

    $notification = $gateway->acceptNotification();
    $notification->send();
```
    

