<?php

namespace Omnipay\Ceca\Message;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Omnipay\Ceca\Encryptor\Encryptor;
use Omnipay\Ceca\Helpers\SignatureHelper;
use Omnipay\Ceca\Helpers\UtilsHelper;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Ceca (Redsys) Purchase Request
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 * @author NitsNets Studio <github@nitsnets.com>
 */
class PurchaseRequest extends AbstractRequest
{

    /**
     * @var string
     */
    protected $liveEndpoint = 'https://pgw.ceca.es/tpvweb/tpv/compra.action';
    /**
     * @var string
     */
    protected $testEndpoint = 'https://tpv.ceca.es/tpvweb/tpv/compra.action';


    /**
     * @param $MerchantID
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setMerchantID($MerchantID)
    {
        return $this->setParameter('MerchantID', $MerchantID);
    }

    /**
     * @param $AcquirerBIN
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setAcquirerBIN($AcquirerBIN)
    {
        return $this->setParameter('AcquirerBIN', $AcquirerBIN);
    }

    /**
     * @param $TerminalID
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setTerminalID($TerminalID)
    {
        return $this->setParameter('TerminalID', $TerminalID);
    }

    public function setEncryptionKey($key)
    {
        return $this->setParameter('clave_encriptacion', $key);
    }

    /**
     * @param $Exponente
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setExponente($Exponente)
    {
        return $this->setParameter('Exponente', $Exponente);
    }

    /**
     * @param $Idioma
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setIdioma($Idioma)
    {
        return $this->setParameter('Idioma', $Idioma);
    }

    /**
     * @param $Cifrado
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setCifrado($Cifrado)
    {
        return $this->setParameter('Cifrado', $Cifrado);
    }


    /**
     * @param $url
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setURL_OK($url)
    {
        return $this->setParameter('URL_OK', $url);
    }

    /**
     * @param $url
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setURL_NOK($url)
    {
        return $this->setParameter('URL_NOK', $url);
    }

    /**
     * @param $transactionId
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setTransactionId($value)
    {

        if(strstr($value,'-')){
            // Since CECA does not allow retry in less than 24 houres, we use a combination of
            // transaction ID and a random number to avoid conflicts, and we use "-" as separator.
            throw new \Exception("Transaction ID must not contain the character '-'");
        }

        $maxLen = 50;
        $suffixLen = 13;
        $validLen = $maxLen - $suffixLen;
        $transactionId = UtilsHelper::getTransactionId($value);
        if (strlen($transactionId) > $maxLen) {
            throw new \InvalidArgumentException("Transaction ID must be less than $validLen characters long");
        }

        return $this->setParameter('Num_operacion', $transactionId);
    }


    /**
     * @param $Descripcion
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setDescription($Descripcion)
    {
        return $this->setParameter('Descripcion', $Descripcion);
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $data = array();

        $clave_encriptacion = $this->getParameter('clave_encriptacion');
        $dataParams = [
            'MerchantID',
            'AcquirerBIN',
            'TerminalID',
            'Num_operacion',
            'Exponente',
            'URL_OK',
            'URL_NOK',
            'Cifrado',
            'Idioma',
            'Descripcion'
        ];

        foreach ($dataParams as $param) {
            $data[$param] = $this->getParameter($param);
        }

        $currency = new Currency($this->getCurrency());

        $currencies = new ISOCurrencies();
        $data['TipoMoneda'] = $currencies->numericCodeFor($currency);

        $data['Importe'] = (float)$this->getAmount();
        // this param is frozen to SSL since it is the only supported payment method
        $data['Pago_soportado'] = 'SSL';

        $data['Firma'] = $this->generateSignature($data, $clave_encriptacion);
        return $data;

    }

    /**
     * @param $data
     * @return \Omnipay\Ceca\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getEndpointBase();
    }

    /**
     * @return string
     */
    public function getEndpointBase()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @param $parameters
     * @param $clave_encriptacion
     * @return string
     */
    protected function generateSignature($parameters, $clave_encriptacion)
    {
        $signParams = [
            $clave_encriptacion,
            $parameters['MerchantID'],
            $parameters['AcquirerBIN'],
            $parameters['TerminalID'],
            $parameters['Num_operacion'],
            $parameters['Importe'],
            $parameters['TipoMoneda'],
            $parameters['Exponente'],
            $parameters['Cifrado'],
            $parameters['URL_OK'],
            $parameters['URL_NOK']
        ];
        return SignatureHelper::sign($signParams, $clave_encriptacion);

    }
}
