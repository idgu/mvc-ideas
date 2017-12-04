<?php
/**
 * User: idgu
 * Date: 18.11.2017
 * Time: 19:21
 */

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Home extends \Core\Controller
{
    public function indexAction() {
        View::renderTemplate('/Home/index.html');
    }
}