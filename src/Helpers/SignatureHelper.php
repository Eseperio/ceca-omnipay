<?php

namespace Omnipay\Ceca\Helpers;
/**
 * Utility to sign and verify signatures
 */
class SignatureHelper
{

    private static $algorithm = 'sha256';

    public static function sign($params, $key)
    {
        $signature = '';
        foreach ($params as $param) {
            $signature .= $param;
        }
        return hash(self::$algorithm, $signature);
    }

    /**
     * Ensures that the signature matches the expected signature for the given parameters
     * @param $params
     * @param $signature
     * @param $key
     * @return bool
     */
    public static function check($params, $signature, $key)
    {
        return $signature === self::sign($params, $key);
    }



}
