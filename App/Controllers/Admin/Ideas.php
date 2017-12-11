<?php
/**
 * User: idgu
 * Date: 10.12.2017
 * Time: 13:47
 */

namespace App\Controllers\Admin;

use \App\Models\Idea;
use \Core\View;
use \App\Flash;
use \App\Models\IdeasCategories;
use \App\Auth;
use \App\Validator;
use \App\Form;
use \App\Input;

class Ideas extends Authenticatedadmin
{


    public function indexAction()
    {

        if (!isset($this->route_params['numpage'])) {
            $numpage = 1;
        } else {
            $numpage = $this->route_params['numpage'];
        }

        $ideasCount = Idea::getIdeasCount(true);
        $numpages = ceil($ideasCount/3);
        if ($numpages == 0) {
            $numpages = 1;
        }
        $start_record = ($numpage-1) * 3;
        $numlist = $start_record +1;

        $ideas = Idea::getAll('add_date/desc/'. $start_record .', 5', true );

        View::renderTemplate('Admin/Ideas/index.html', [
            'ideas' => $ideas,
            'numlist' => $numlist,
            'numpages' => $numpages,
            'numpage' => $numpage
        ]);
    }



    public function waitingAction()
    {

        if (!isset($this->route_params['numpage'])) {
            $numpage = 1;
        } else {
            $numpage = $this->route_params['numpage'];
        }

        $ideasCount = Idea::getIdeasCount(false, true);

        $numpages = ceil($ideasCount/3);
        if ($numpages == 0) {
            $numpages = 1;
        }
        $start_record = ($numpage-1) * 3;
        $numlist = $start_record +1;

        $ideas = Idea::getAll('add_date/desc/'. $start_record .', 3', false, true);

        View::renderTemplate('Admin/Ideas/waiting.html', [
            'ideas' => $ideas,
            'numlist' => $numlist,
            'numpages' => $numpages,
            'numpage' => $numpage
        ]);
    }


    public function acceptAction()
    {
        $idea = Idea::get($this->route_params['userid']);
        if ($idea -> accept()) {
            Flash::addMessage('Pomyślnie aktywowano pomysł!', Flash::WARNING);
            $this->redirect( '/admin/ideas/waiting');
        }
    }



    public function showAction()
    {

        $idea = Idea::get($this->route_params['userid']);

        if ($idea) {
            View::renderTemplate('/Admin/Ideas/show.html',[
                'idea' => $idea
            ]);
        } else {
            Flash::addMessage('Pomysł nie istnieje', Flash::WARNING);
            $this->redirect( '/admin/ideas/index');
        }
    }

    public function addAction()
    {
        $categories = IdeasCategories::getAll();
        View::renderTemplate('Admin/Ideas/add.html',[
            'categories' => $categories,
            'token_form' => Input::generateFormToken()
        ]);
    }



    public function createAction()
    {
        $categories = IdeasCategories::getAll();

        $user = Auth::getUser();


        $idea = new Idea($_POST);

        $validator = new Validator($idea);

        $validator->add(new Form('Nazwa pomysłu', 'name', [
            'maxlength' =>120,
            'minlength' =>10,
            'notExistDb' => 'ideas/name'
        ]));

        $validator -> add(new Form('Opis','content', [
            'minlength' =>10
        ]));

        $validator ->add($password = new Form('Wkład','contribution', [
            'number'=> true
        ]));

        $validator -> add(new Form('Kategoria','category', [
            'existDb' => 'ideas_categories/id'
        ]));


        if ($idea->save($validator, $user->id, true)) {
            Flash::addMessage('Pomysł został dodany!');
            $this->redirect('/admin/ideas/index/');
        } else {
            View::renderTemplate('/Admin/Ideas/add.html', [
                'idea'       => $idea,
                'categories' => $categories,
                'token_form' => Input::generateFormToken()
            ]);
        }
    }



    public function editAction()
    {
        $idea = Idea::get($this->route_params['userid']);
        $categories = IdeasCategories::getAll();

        if ($idea){
            View::renderTemplate('/Admin/Ideas/edit.html',[
                'idea' => $idea,
                'token_form' => Input::generateFormToken(),
                'categories' => $categories
            ]);
        } else {
            Flash::addMessage('Podany pomysł nie istnieje!', Flash::WARNING);
            $this->redirect( '/admin/ideas/index');
        }
    }



    public function updateAction()
    {

        $idea= Idea::get($this->route_params['userid']);

        if ($idea){

            $categories = IdeasCategories::getAll();
            $user = Auth::getUser();


            $validator = new Validator($idea);

            $validator -> add(new Form('Opis','content', [
                'minlength' =>10
            ]));

            $validator ->add($password = new Form('Wkład','contribution', [
                'number'=> true
            ]));

            $validator -> add(new Form('Kategoria','category', [
                'existDb' => 'ideas_categories/id'
            ]));



            if ($idea->update($validator, $_POST)) {

                Flash::addMessage('Pomysł został zaktualizowany!');
                $this->redirect('/admin/ideas/show/'.$idea->id);

            } else {

                View::renderTemplate('Admin/ideas/edit.html', [
                    'idea'=>$idea,
                    'token_form'=> Input::generateFormToken()
                ]);

            }
        } else {
            Flash::addMessage('Pomysł nie istnieje!', Flash::WARNING);
            $this->redirect( '/admin/ideas/index');
        }
    }


    public function deleteAction()
    {
        $idea = Idea::get($this->route_params['userid']);

        if ($idea){
            $idea->delete();
            Flash::addMessage('Pomysł został usunięty.', Flash::WARNING);
            $this->redirect( '/admin/ideas/index');
        } else {
            Flash::addMessage('Pomysł nie istnieje!', Flash::WARNING);
            $this->redirect( '/admin/ideas/index');
        }
    }


}