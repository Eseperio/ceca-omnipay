<?php

namespace Omnipay\Ceca\Dictionaries;

/**
 * These are the payment methods supported by CECA
 */
class PayMethods
{

    const NORMAL = 'C';
    const BIZUM = 'E';
    const AMEX = 'A';
    const PREAUTH = 'G';
    const AUTHORIZATION_CHARGE = 'H';
}
