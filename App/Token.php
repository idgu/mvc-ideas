<?php
/**
 * User: idgu
 * Date: 22.11.2017
 * Time: 08:45
 */

namespace App;

use \App\Auth;

class Token
{
    protected $token;

    public function __construct($token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        } else {
            $this->token = bin2hex(random_bytes(16)); // 16 bytes = 128bits = 32hex
        }
    }

    public function getValue()
    {
        return $this->token;
    }

    public function getHash()
    {
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);
    }

}