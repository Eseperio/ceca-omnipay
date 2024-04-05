<?php

namespace Omnipay\Ceca\Notifications;

use Omnipay\Ceca\Helpers\SignatureHelper;
use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is the class used to process the notification received from the TPV
 */
class AcceptNotification implements NotificationInterface
{

    /**
     * @var string key used to encrypt the data
     */
    private string $encryptionKey;
    /**
     * @var string error message
     */
    private string $error;

    /**
     * @param $encryptionKey
     */
    public function __construct($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * @var array
     */
    private array $requestData;

    /**
     * @return array|mixed
     */
    public function getData()
    {
        if (empty($this->request)) {
            $this->request = Request::createFromGlobals();
            $this->requestData = $this->request->request->all();
        }
        return $this->requestData;
    }

    /**
     * @return mixed|string
     */
    public function getTransactionReference()
    {
        $returnedParameters = $this->getData();
        // split on - to get the order id
        $transactionId = $returnedParameters['Num_operacion'];
        $transactionId = explode('-', $transactionId);
        return current($transactionId);
    }


    /**
     * @return string the status of the transaction
     */
    public function getTransactionStatus()
    {

        if (!$this->checkSignature()) {
            $this->error = 'Bad signature';
            return NotificationInterface::STATUS_FAILED;
        }

        return NotificationInterface::STATUS_COMPLETED;
    }

    /**
     * Checks signature using data received from tpv
     * @return bool
     */
    private function checkSignature()
    {
        $data = $this->getData();
        $expectedSignature = $data['Firma'];
        $signatureData = [$this->encryptionKey];
        $data = [
            'MerchantID',
            'AcquirerBIN',
            'TerminalID',
            'Num_operacion',
            'Importe',
            'TipoMoneda',
            'Exponente',
            'Referencia'
        ];

        foreach ($data as $param) {
            $signatureData[] = $data[$param];
        }
        $signature = strtolower(SignatureHelper::sign($signatureData, $this->encryptionKey));

        return $signature == $expectedSignature;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->error;
    }
}
