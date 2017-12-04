<?php
/**
 * User: idgu
 * Date: 23.11.2017
 * Time: 10:22
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
Use \App\Input;
use \App\Form;
use \App\Validator;

class Password extends Authenticatednot
{
    public function forgotAction()
    {
        View::renderTemplate('/Password/forgot.html', [
            'token_form' => Input::generateFormToken()
        ]);
    }




    public function requestReset()
    {
        $user = User::sendPasswordReset($_POST['email']);

        if(empty($user->errors)) {
            View::renderTemplate('/Password/reset_requested.html');
        } else {
            View::renderTemplate('/Password/forgot.html', [
                'user'       => $user,
                'token_form' => Input::generateFormToken()
            ]);
        }
    }



    public function resetAction()
    {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);
        View::renderTemplate('/Password/reset.html', [
            'user'          => $user,
            'token'         => $token,
            'token_form'    => Input::generateFormToken()
        ]);

    }



    public function resetPasswordAction()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);


        $validator = new Validator($user);
        $validator -> add($password = new Form('Password','password', [
            'maxlength'=>32,
            'minlength'=>6,
            'oneNumber' => true,
            'oneLetter' => true
        ]));

        $validator->add(new Form('Password Confirm','password_confirmation', [
            'equals' => $password
        ]));

        if ($user->resetPassword($validator, $_POST['password'], $_POST['password_confirmation'])) {
            View::renderTemplate('/Password/reset_success.html');
        } else {
            View::renderTemplate('/Password/reset.html', [
                'token' => $token,
                'user' => $user,
                'token_form' => Input::generateFormToken()
            ]);
        }
    }



    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {
            return $user;
        } else {
            View::renderTemplate('/Password/token_expired.html');
            exit;
        }
    }
}