<?php
/**
 * User: idgu
 * Date: 27.11.2017
 * Time: 12:03
 */

namespace App\Controllers;


abstract class Authenticatednot extends \Core\Controller
{

    public function before()
    {
        $this->notRequireLogin();
    }

}