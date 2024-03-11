<?php

namespace Omnipay\Ceca;

use Omnipay\Ceca\Dictionaries\PayMethods;
use Symfony\Component\HttpFoundation\Request;
use Omnipay\Common\AbstractGateway;
use Omnipay\Ceca\Message\CallbackResponse;

/**
 * Ceca (Redsys) Gateway
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 * @author NitsNets Studio <github@nitsnets.com>
 */
class Gateway extends AbstractGateway
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Ceca';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'MerchantID' => '',
            'AcquirerBIN' => '',
            'TerminalID' => '00000003',
            'TipoMoneda' => '978',
            'Exponente' => '2',
            'Idioma' => '1',
            'Cifrado' => 'SHA2',
            'Pago_soportado' => 'SSL',
            'testMode' => false,
            'Tipo_operacion' => PayMethods::NORMAL,
        );
    }

    //Set merchanID - required

    /**
     * @param $MerchantID
     * @return \Omnipay\Ceca\Gateway
     */
    public function setMerchantID($MerchantID)
    {
        return $this->setParameter('MerchantID', $MerchantID);
    }
    //Set AcquirerBIN - required

    /**
     * @param $AcquirerBIN
     * @return \Omnipay\Ceca\Gateway
     */
    public function setAcquirerBIN($AcquirerBIN)
    {
        return $this->setParameter('AcquirerBIN', $AcquirerBIN);
    }   
    //Set TerminalID - required

    /**
     * @param $TerminalID
     * @return \Omnipay\Ceca\Gateway
     */
    public function setTerminalId($TerminalID)
    {
        // pad value with zeros to 8 characters as in CECA documentation.
        $TerminalID = str_pad($TerminalID, 8, '0', STR_PAD_LEFT);
        return $this->setParameter('TerminalID', $TerminalID);
    }
    //Set TipoMoneda - required

    /**
     * @param $TipoMoneda
     * @return \Omnipay\Ceca\Gateway
     */
    public function setTipoMoneda($TipoMoneda)
    {
        return $this->setParameter('TipoMoneda', $TipoMoneda);
    }
    //Set Idioma - required

    /**
     * @param $Idioma
     * @return \Omnipay\Ceca\Gateway
     */
    public function setIdioma($Idioma)
    {
        return $this->setParameter('Idioma', $Idioma);
    }
    //Set Idioma - required

    /**
     * @param $clave_encriptacion
     * @return \Omnipay\Ceca\Gateway
     */
    public function setEncryptionKey($clave_encriptacion)
    {
        return $this->setParameter('clave_encriptacion', $clave_encriptacion);
    }
    //Set Idioma - required

    /**
     * @param $url
     * @return \Omnipay\Ceca\Gateway
     */
    public function setUrlOk($url)
    {
        return $this->setParameter('URL_OK', $url);
    }
    //Set Idioma - required

    /**
     * @param $url
     * @return \Omnipay\Ceca\Gateway
     */
    public function setUrlNoOk($url)
    {
        return $this->setParameter('URL_NOK', $url);
    }

    /**
     * @param $bizum
     * @return \Omnipay\Ceca\Gateway
     */
    public function setBizum($bizum)
    {
        // change tipo operacion to E
        $this->setParameter('TipoOperacion', 'E');
        return $this->setParameter('bizum', $bizum);
    }


    /**
     * @param array $parameters
     * @return \Omnipay\Ceca\Message\PurchaseRequest|\Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ceca\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Ceca\Message\CompletePurchaseRequest', $parameters);
    }


    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     * @throws \Omnipay\Ceca\Exception\BadSignatureException
     * @throws \Omnipay\Ceca\Exception\CallbackException
     */
    public function checkCallbackResponse(Request $request)
    {
        $response = new CallbackResponse($request, $this->getParameter('clave_encriptacion'));

        return $response->isSuccessful();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function decodeCallbackResponse(Request $request)
    {

        $returnedParameters = [];
        $returnedParameters['MerchantID'] = $request->get('MerchantID');
        $returnedParameters['AcquirerBIN'] = $request->get('AcquirerBIN');
        $returnedParameters['TerminalID'] = $request->get('TerminalID');
        $returnedParameters['Num_operacion'] = $request->get('Num_operacion');
        $returnedParameters['Importe'] = $request->get('Importe');
        $returnedParameters['TipoMoneda'] = $request->get('TipoMoneda');
        $returnedParameters['Exponente'] = $request->get('Exponente');
        $returnedParameters['Referencia'] = $request->get('Referencia');
        $returnedParameters['Num_aut'] = $request->get('Num_aut');
        $returnedParameters['BIN'] = $request->get('BIN');
        $returnedParameters['FinalPAN'] = $request->get('FinalPAN');
        $returnedParameters['Cambio_moneda'] = $request->get('Cambio_moneda');
        $returnedParameters['Pais'] = $request->get('Pais');
        $returnedParameters['Tipo_tarjeta'] = $request->get('Tipo_tarjeta');
        $returnedParameters['Descripcion'] = $request->get('Descripcion');

        return $returnedParameters;
    }
}
