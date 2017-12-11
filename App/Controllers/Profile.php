<?php
/**
 * User: idgu
 * Date: 24.11.2017
 * Time: 23:12
 */

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \app\Models\User;
use \App\Flash;
use \App\Input;
use \App\Validator;
use \App\Form;


class Profile extends Authenticated
{

    public function show()
    {
        if (isset($this->route_params['userid'])){


            $user = User::findById($this->route_params['userid']);
            if ($user) {
                View::renderTemplate('/Profile/show.html', [
                    'user' => $user
                ]);
            } else {
                Flash::addMessage('Użytkownik nie istnieje');
                $this->redirect('/');
            }



        } else {


            $user = Auth::getUser();
            if (!$user){
                Flash::addMessage('Użytkownik nie istnieje');
                $this->redirect('/');
            }
            View::renderTemplate('/Profile/show.html', [
                'user' => $user
            ]);


        }
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

        $validator->add(new Form('Nazwa użytkownika', 'name', [
            'maxlength' =>32,
            'minlength' =>4
        ]));

        $validator ->add($password = new Form('Hasło','password', [
            'maxlength'=>32,
            'minlength'=>6,
            'oneNumber' => true,
            'oneLetter' => true,
            'notRequired' => true
        ]));

        $validator->add(new Form('Potwierdzenie hasła','password_confirmation', [
            'equals' => $password
        ]));

        if ($user->updateProfile($validator, $_POST)) {

            Flash::addMessage('Konto zostało zaktualizowane');
            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('/Profile/edit.html', [
                'user'=>$user,
                'token_form'=> Input::generateFormToken()
            ]);

        }
    }
}