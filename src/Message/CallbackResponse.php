<?php

namespace Omnipay\Ceca\Message;

use Omnipay\Ceca\Encryptor\Encryptor;
use Symfony\Component\HttpFoundation\Request;
use Omnipay\Ceca\Exception\BadSignatureException;
use Omnipay\Ceca\Exception\CallbackException;

/**
 * @deprecated
 * Ceca (Redsys)  Callback Response
 */
class CallbackResponse
{

    private $request;
    private $merchantKey;

    public function __construct(Request $request, $merchantKey)
    {
        $this->request = $request;
        $this->merchantKey = $merchantKey;
        $this->error = '';
    }

    /**
     * Check callback response from tpv
     *
     * @return boolean
     * @throws BadSignatureException
     * @throws CallbackException
     */
    public function isSuccessful()
    {
        $returnedParameters = [];
        $returnedParameters['MerchantID'] = $this->request->get('MerchantID');
        $returnedParameters['AcquirerBIN'] = $this->request->get('AcquirerBIN');
        $returnedParameters['TerminalID'] = $this->request->get('TerminalID');
        $returnedParameters['Num_operacion'] = $this->request->get('Num_operacion');
        $returnedParameters['Importe'] = $this->request->get('Importe');
        $returnedParameters['TipoMoneda'] = $this->request->get('TipoMoneda');
        $returnedParameters['Exponente'] = $this->request->get('Exponente');
        $returnedParameters['Referencia'] = $this->request->get('Referencia');
        
        $firma = $this->request->get('Firma');

        if (!$this->checkSignature($returnedParameters, $firma)) {
            throw new BadSignatureException();
        }

        return true;
    }

    private function checkSignature($data, $expectedSignature)
    {
        //Clave_encriptacion+MerchantID+AcquirerBIN+TerminalID+Num_operacion+Importe+TipoMoneda+Exponente+Referencia
        $signature = 
            $this->merchantKey 
            . $data['MerchantID'] 
            . $data['AcquirerBIN'] 
            . $data['TerminalID'] 
            . $data['Num_operacion'] 
            . $data['Importe'] 
            . $data['TipoMoneda'] 
            . $data['Exponente'] 
            . $data['Referencia'];

        $signature = strtolower(sha1($signature));

        return $signature == $expectedSignature;
    }
}
