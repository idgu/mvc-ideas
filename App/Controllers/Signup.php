<?php
/**
 * User: idgu
 * Date: 21.11.2017
 * Time: 17:48
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Input;
use \App\Form;
use \App\Flash;
use \App\Validator;

class Signup extends Authenticatednot
{

    public function newAction()
    {
        View::renderTemplate('/Signup/new.html', [
            'token_form' => Input::generateFormToken()
        ]);
    }


    public function createAction()
    {
        $user = new User($_POST);


        $validator = new Validator($user);

        $validator->add(new Form('Username', 'name', [
            'maxlength' =>32,
            'minlength' =>4
        ]));

        $validator -> add(new Form('Email','email', [
            'email'=> true,
            'notExistDb' => 'users/email'
        ]));

        $validator ->add($password = new Form('Password','password', [
            'maxlength'=>32,
            'minlength'=>6,
            'oneNumber' => true,
            'oneLetter' => true
        ]));

        $validator->add(new Form('Password','password_confirmation', [
            'equals' => $password
        ]));


       if ($user->save($validator)) {

           $user->sendActivationEmail();

           $this->redirect('/signup/success');
       } else {
           View::renderTemplate('/Signup/new.html', [
               'user'       => $user,
               'token_form' => Input::generateFormToken()
           ]);
       }
    }


    public function successAction()
    {
        View::renderTemplate('/Signup/success.html');
    }





    public function resendActivationEmailAction()
    {
        View::renderTemplate('/Signup/resend_activation_email.html', [
            'token_form' => Input::generateFormToken()
        ]);
    }

    public function requestResendAction()
    {
        $user = User::resendActivationEmail($_POST['email']);
        if(empty($user->errors)) {
            $this->redirect('/signup/resend-success');
        } else {
            View::renderTemplate('/Signup/resend_activation_email.html', [
                'token_form' => Input::generateFormToken(),
                'user' => $user
            ]);
        }
    }

    public function resendSuccessAction()
    {
        View::renderTemplate('/Signup/resend_email_success.html');
    }






    public function activateAction()
    {
        User::activate($this->route_params['token']);
        $this->redirect('/signup/activated');
    }


    public function activatedAction()
    {
        View::renderTemplate('/Signup/activated.html');
    }

}