<?php
/**
 * User: idgu
 * Date: 19.11.2017
 * Time: 12:36
 */

namespace App\Controllers\Admin;

use App\Config;
use \Core\View;
use \App\Models\User;
use App\Flash;
use \App\Validator;
use \App\Input;
use \App\Form;

class Users extends Authenticatedadmin
{


    public function indexAction()
    {

        if (!isset($this->route_params['numpage'])) {
            $numpage = 1;
        } else {
            $numpage = $this->route_params['numpage'];
        }

        $usersCount = User::getUsersCount();
        $numpages = ceil($usersCount/5);
        $start_record = ($numpage-1) * 5;
        $numlist = $start_record +1;

        $users = User::getAllUsers('join_date/desc/'. $start_record .', 5' );


        View::renderTemplate('/Admin/Users/index.html', [
            'numlist' => $numlist,
            'users' => $users,
            'numpages' => $numpages,
            'numpage' => $numpage
        ]);
    }




    public function addAction()
    {
        View::renderTemplate('/Admin/Users/add.html');
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


        if ($user->save($validator, true)) {
            Flash::addMessage('User added!', Flash::SUCCESS);
            $this->redirect('/admin');
        } else {
            View::renderTemplate('/Admin/Users/add.html', [
                'user'       => $user,
                'token_form' => Input::generateFormToken()
            ]);
        }

    }




    public function editAction()
    {
        $user = User::findById($this->route_params['userid']);
        if ($user){
            View::renderTemplate('/Admin/Users/edit.html',[
                'user' => $user,
                'token_form' => Input::generateFormToken()
            ]);
        } else {
            Flash::addMessage('User not found', Flash::WARNING);
            $this->redirect( '/admin/users/index');
        }
    }





    public function updateAction()
    {

        $user = User::findById($this->route_params['userid']);

        if ($user){

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
//            var_dump($_POST);
//            die();

            if ($user->updateProfile($validator, $_POST)) {

                if ((int)$_POST['permission'] == 0) {
                    $user->setUserPermission();
                } else {
                    $user->setAdminPermission();
                }

                Flash::addMessage('Changes saved');
                $this->redirect('/admin/users/show/'.$user->id);

            } else {

                View::renderTemplate('Admin/Users/edit.html', [
                    'user'=>$user,
                    'token_form'=> Input::generateFormToken()
                ]);

            }
        } else {
            Flash::addMessage('User not found', Flash::WARNING);
            $this->redirect( '/admin/users/index');
        }
    }






    public function deleteAction()
    {
        $user = User::findById($this->route_params['userid']);

        if ($user){
            $user->delete();
            Flash::addMessage('User deleted.', Flash::WARNING);
            $this->redirect( '/admin/users/index');
        } else {
            Flash::addMessage('User not found', Flash::WARNING);
            $this->redirect( '/admin/users/index');
        }
    }




    public function showAction()
    {

        $user = User::findById($this->route_params['userid']);

        if ($user) {
            View::renderTemplate('/Admin/Users/show.html',[
                'user' => $user
            ]);
        } else {
            Flash::addMessage('User not found', Flash::WARNING);
            $this->redirect( '/admin/users/index');
        }
    }
}