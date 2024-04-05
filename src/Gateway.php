<?php

namespace Omnipay\Ceca;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Omnipay\Ceca\Dictionaries\PayMethods;
use Omnipay\Ceca\Helpers\SignatureHelper;
use Omnipay\Ceca\Helpers\UtilsHelper;
use Omnipay\Ceca\Notifications\AcceptNotification;
use Omnipay\Common\AbstractGateway;

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
            'currency' => 'EUR',
            'Exponente' => '2',
            'Idioma' => '1',
            'Cifrado' => 'SHA2',
            'Pago_soportado' => 'SSL',
            'testMode' => false,
            'Tipo_operacion' => PayMethods::NORMAL,
        );
    }

    /**
     * @param $value
     * @return \Omnipay\Ceca\Gateway|void
     */
    public function setCurrency($value)
    {
        if (is_numeric($value)) {
            return new Currency($value);
        }
        return parent::setCurrency($value);
    }

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
    public function purchase(array $options = array())
    {
        return $this->createRequest('\Omnipay\Ceca\Message\PurchaseRequest', $options);
    }

    /**
     * @param $key
     * @return \Omnipay\Ceca\Gateway
     */
    public function setEncryptionKey($key)
    {
        return $this->setParameter('encryptionKey', $key);
    }

    /**
     * @param array $options
     * @return \Omnipay\Ceca\Notifications\AcceptNotification
     */
    public function acceptNotification(): AcceptNotification
    {
        return new AcceptNotification($this->getParameter('encryptionKey'));
    }

    /**
     * Emulates the http notification from the bank.
     * Creates a form with the required fields and sends it to the notification url.
     * @return void
     */
    public function emulateNotification($notificationUrl, $transactionId, $amount)
    {
        // clear output buffer
        ob_clean();
        $transactionId = UtilsHelper::getTransactionId($transactionId);
        $key = $this->getParameter('encryptionKey');
        $data = [
            'key' => $key,
            'MerchantID' => $this->getParameter('MerchantID'),
            'AcquirerBIN' => $this->getParameter('AcquirerBIN'),
            'TerminalID' => $this->getParameter('TerminalID'),
            'Num_operacion' => $transactionId,
            'Importe' => $amount,
            'TipoMoneda' => $this->getParameter('currency'),
            'Exponente' => $this->getParameter('Exponente'),
            'Referencia' => '',
        ];
        $signature = SignatureHelper::sign($data, $key);
        $data['Firma'] = $signature;
        // create the form
        echo '<form method="POST" action="' . $notificationUrl . '" onload="submit();">';
        foreach ($data as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
//        echo '<input type="submit" value="Submit">';
        echo '</form>';

    }

}
