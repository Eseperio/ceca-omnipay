<?php

namespace Omnipay\Ceca\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Ceca\Encryptor\Encryptor;

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
    protected $liveEndpoint = 'https://tpv.ceca.es/tpvweb/tpv/compra.action';
    /**
     * @var string
     */
//    protected $testEndpoint = 'http://tpv.ceca.es:8000/cgi-bin/tpv';
    protected $testEndpoint = 'https://pgw.ceca.es/tpvweb/tpv/compra.action';


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

    /**
     * @param $TipoMoneda
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setTipoMoneda($TipoMoneda)
    {
        return $this->setParameter('TipoMoneda', $TipoMoneda);
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
     * @param $clave_encriptacion
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setEncryptionKey($clave_encriptacion)
    {
        return $this->setParameter('clave_encriptacion', $clave_encriptacion);
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
     * @param $Num_operacion
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setNumOperacion($Num_operacion)
    {
        return $this->setParameter('Num_operacion', $Num_operacion);
    }

    /**
     * @param $Pago_soportado
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setPagoSoportado($Pago_soportado)
    {
        return $this->setParameter('Pago_soportado', $Pago_soportado);
    }

    /**
     * @param $Descripcion
     * @return \Omnipay\Ceca\Message\PurchaseRequest
     */
    public function setDescripcion($Descripcion)
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

        $data['MerchantID'] = $this->getParameter('MerchantID');
        $data['AcquirerBIN'] = $this->getParameter('AcquirerBIN');
        $data['TerminalID'] = $this->getParameter('TerminalID');

        $data['Num_operacion'] = $this->getParameter('Num_operacion');
        $data['Importe'] = (float)$this->getAmount();
        $data['TipoMoneda'] = $this->getParameter('TipoMoneda');
        $data['Exponente'] = $this->getParameter('Exponente');
        
        $data['URL_OK'] = $this->getParameter('URL_OK');
        $data['URL_NOK'] = $this->getParameter('URL_NOK');
        $data['Cifrado'] = $this->getParameter('Cifrado');
        $data['Idioma'] = $this->getParameter('Idioma');
        $data['Pago_soportado'] = $this->getParameter('Pago_soportado');
        $data['Descripcion'] = $this->getParameter('Descripcion');

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
        $signature = 
            $clave_encriptacion 
            . $parameters['MerchantID'] 
            . $parameters['AcquirerBIN'] 
            . $parameters['TerminalID'] 
            . $parameters['Num_operacion'] 
            . $parameters['Importe'] 
            . $parameters['TipoMoneda'] 
            . $parameters['Exponente'] 
            . $parameters['Cifrado'] 
            . $parameters['URL_OK'] 
            . $parameters['URL_NOK'];

        return hash('sha256', $signature);
    }
}
