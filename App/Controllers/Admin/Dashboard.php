<?php
/**
 * User: idgu
 * Date: 27.11.2017
 * Time: 12:58
 */

namespace App\Controllers\Admin;

use \App\Auth;
use \Core\View;
use \App\Models\User;

class Dashboard extends Authenticatedadmin
{

    public function indexAction()
    {
        $users = User::getAllUsers('join_date/desc/5');
        $usersCount = User::getUsersCount();
        View::renderTemplate('/Admin/Dashboard/index.html',[
            'users' => $users,
            'usersCount' => $usersCount
        ]);
    }
}