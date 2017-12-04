<?php
/**
 * User: idgu
 * Date: 26.11.2017
 * Time: 17:26
 */

namespace App;

use \Core\View;
use \Exception;
use \App\Token;
use \App\Auth;

class Input
{
    public static function postExistsOrDie()
    {
        if (empty($_POST)) {
            throw new Exception('Form wasnt send!');
        }
    }

    public static function generateFormToken()
    {
        $token = new Token();
        $tokenValue = $token->getValue();

        Auth::saveTokenFrom($tokenValue);

        return $tokenValue;
    }


    public static function checkTokenForm($token)
    {
        return Auth::checkTokenForm($token);
    }
}