<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
namespace TD\MasterCard\Helper;

class Crypto
{
    /**
     * validates and associative array that contains a hmac signature against an api key
     * @param $query array
     * @param $securesecret string
     * @return bool
     */
    public static function isValidSignature($query, $securesecret)
    {
        $hashinput = '';
        foreach ($query as $key => $value) {
            if ($key != 'vpc_SecureHash' && $key != 'vpc_SecureHashType' && strlen($value) > 0) {
                $hashinput .= $key . "=" . $value . "&";
            }
        }

        return strtoupper($query['vpc_SecureHash']) == strtoupper(hash_hmac('SHA256', rtrim($hashinput, "&"), pack('H*', $securesecret)));
    }

    /**
     * Get hash
     */
    public function getHash($formFields, $securesecret)
    {
        ksort($formFields);
        $hashinput = '';
        foreach ($formFields as $key => $value) {
            if (strlen($value) > 0) {
                $hashinput .= $key . "=" . $value . "&";
            }
        }
        return strtoupper(hash_hmac('SHA256', rtrim($hashinput, "&"), @pack('H*', $securesecret)));
    }
}