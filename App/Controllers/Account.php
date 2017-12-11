<?php
/**
 * User: idgu
 * Date: 26.11.2017
 * Time: 11:09
 */

namespace App\Controllers;

use \App\Models\User;
use \App\Models\Idea;
use \App\Auth;
use \App\Validator;


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



    public function xhrSearchIdeaByIdAction()
    {
        echo json_encode(Idea::searchIdea($_GET['name']));
    }



    public function xhrVoteAction()
    {
        $user  = Auth::getUser();

        if ($user){
            echo 'work2';
            if ($user->canVote($_GET['ideaid'])) {
                echo'work3';
                if($user->vote($_GET['ideaid'], $_GET['rate'])) {
                    $idea = Idea::get($_GET['ideaid']);
                    $idea->addVote($user->id, $_GET['rate']);
                    echo 'Pomyślnie oddano głos!';
                } else {
                    echo 'Nie udało się zagłosować!';
                }
            }else {
                echo 'Nie możesz głosować!';
            }
        }

    }
}