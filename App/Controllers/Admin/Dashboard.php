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
use \App\Models\Idea;

class Dashboard extends Authenticatedadmin
{

    public function indexAction()
    {
        $users = User::getAllUsers('join_date/desc/5');
        $usersCount = User::getUsersCount();

        $ideas = Idea::getAll('join_date/desc/5', true);
        $ideasCount = Idea::getIdeasCount(true);

        $ideasWaitingCount = Idea::getIdeasCount(false, true);
        View::renderTemplate('/Admin/Dashboard/index.html',[
            'users'             => $users,
            'usersCount'        => $usersCount,
            'ideas'             =>$ideas,
            'ideasCount'        => $ideasCount,
            'ideasWaitingCount' => $ideasWaitingCount
        ]);
    }
}