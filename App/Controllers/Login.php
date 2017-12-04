<?php
/**
 * User: idgu
 * Date: 21.11.2017
 * Time: 19:12
 */

namespace App\Controllers;

use \core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;
use \App\Input;

class Login extends Authenticatednot
{
    public function newAction()
    {
        View::renderTemplate('/Login/new.html', [
            'token_form' => Input::generateFormToken()
        ]);
    }



    public function createAction()
    {

        $user = User::authenticate($_POST['email'], $_POST['password']);
        $remember_me = isset($_POST['remember_me']);


        if ($user) {

            Auth::login($user, $remember_me);
            Flash::addMessage('Login successful');
            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);
            View::renderTemplate('/Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me,
                'token_form' => Input::generateFormToken()
            ]);

        }
    }



    public function destroy()
    {
        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }



    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout successful');
        $this->redirect('/');
    }
}