<?php
/**
 * User: idgu
 * Date: 27.11.2017
 * Time: 12:37
 */

namespace App\Controllers\Admin;


abstract class Authenticatedadmin extends \Core\Controller
{
    protected function before()
    {
        $this->requireAdmin();
    }
}