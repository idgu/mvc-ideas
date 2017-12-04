<?php
/**
 * User: idgu
 * Date: 22.11.2017
 * Time: 00:27
 */

namespace App\Controllers;


abstract class Authenticated extends \Core\Controller
{
    public function before()
    {
        $this->requireLogin();
    }
}