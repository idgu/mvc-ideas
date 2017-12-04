<?php
/**
 * User: idgu
 * Date: 24.11.2017
 * Time: 23:12
 */

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Input;
use \App\Validator;
use \App\Form;


class Profile extends Authenticated
{

    public function showAction()
    {
        View::renderTemplate('/Profile/show.html');
    }

    public function editAction()
    {
        View::renderTemplate('/Profile/edit.html',[
            'token_form' => Input::generateFormToken()
        ]);
    }


    public function updateAction()
    {
        $user = Auth::getUser();

        $validator = new Validator($user);

        $validator->add(new Form('Username', 'name', [
            'maxlength' =>32,
            'minlength' =>4
        ]));

        $validator ->add($password = new Form('Password','password', [
            'maxlength'=>32,
            'minlength'=>6,
            'oneNumber' => true,
            'oneLetter' => true,
            'notRequired' => true
        ]));

        $validator->add(new Form('Password','password_confirmation', [
            'equals' => $password
        ]));

        if ($user->updateProfile($validator, $_POST)) {

            Flash::addMessage('Changes saved');
            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('/Profile/edit.html', [
                'user'=>$user,
                'token_form'=> Input::generateFormToken()
            ]);

        }
    }
}