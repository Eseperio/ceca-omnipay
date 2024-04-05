<?php

namespace Omnipay\Ceca\Helpers;
/**
 * Contains tools useful for the library
 */
class UtilsHelper
{

    /**
     * Suffix transaction id to prevent Ceca payment block.
     * Ceca blocks payments with the same transaction id during 24 hours,
     * even if there was an error in payment.
     * @param $id
     * @return string
     */
    public static function getTransactionId($id)
    {
        $sufix = date("dmyhs");
        return $id."-". $sufix;
    }
}


