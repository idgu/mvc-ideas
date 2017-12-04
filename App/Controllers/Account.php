<?php
/**
 * User: idgu
 * Date: 26.11.2017
 * Time: 11:09
 */

namespace App\Controllers;

use \App\Models\User;

class Account extends \Core\Controller
{

    public function xhrValidateEmailAction()
    {
        $is_valid = User::emailExists($_GET['email']);
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function xhrSearchUserByEmailAction()
    {
        echo json_encode(User::searchUser($_GET['name']));
    }
}