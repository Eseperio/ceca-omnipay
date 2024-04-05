<?php

namespace Omnipay\Ceca\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Ceca (Redsys) Purchase Response
 * @method \Omnipay\Ceca\Message\PurchaseRequest getRequest()
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    /**
     * @return false
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * @return true
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return array|mixed
     */
    public function getRedirectData()
    {
        return $this->data;
    }
}
