<?php
/**
 * User: idgu
 * Date: 05.12.2017
 * Time: 10:44
 */

namespace App\Controllers;

use \Core\View;
use \App\Models\IdeasCategories;
use \App\Validator;
use \App\Form;
use \App\Input;
use \App\Models\Idea;
use \App\Auth;
use \App\Flash;

class Ideas extends Authenticated
{

    public function showAll()
    {
        if (!isset($this->route_params['numpage'])) {
            $numpage = 1;
        } else {
            $numpage = $this->route_params['numpage'];
        }

        $ideasCount = Idea::getIdeasCount(true);
        $numpages = ceil($ideasCount / 3);
        if ($numpages == 0) {
            $numpages = 1;
        }
        $start_record = ($numpage - 1) * 3;
        $numlist = $start_record + 1;

        $ideas = Idea::getAll('add_date/desc/' . $start_record . ', 3', true);

        View::renderTemplate('/Ideas/show_all.html', [
            'ideas' => $ideas,
            'numlist' => $numlist,
            'numpages' => $numpages,
            'numpage' => $numpage
        ]);

    }

    public function rank()
    {
        if (!isset($this->route_params['numpage'])) {
            $numpage = 1;
        } else {
            $numpage = $this->route_params['numpage'];
        }

        $ideasCount = Idea::getIdeasCount(true);
        $numpages = ceil($ideasCount / 3);
        if ($numpages == 0) {
            $numpages = 1;
        }
        $start_record = ($numpage - 1) * 3;
        $numlist = $start_record + 1;

        $ideas = Idea::getAll('ranking/desc/' . $start_record . ', 3', true);

        View::renderTemplate('/Ideas/ranking.html', [
            'ideas' => $ideas,
            'numlist' => $numlist,
            'numpages' => $numpages,
            'numpage' => $numpage
        ]);
    }


    public function show()
    {
        if (isset($this->route_params['idea'])) {
            $idea = Idea::get($this->route_params['idea']);

            if ($idea) {

                if (!$idea->is_active) {
                    Flash::addMessage('Pomysł nie został jeszcze zaakceptowany przez Administratora!', FLASH::WARNING);
                    $this->redirect('/ideas');
                }

                $user = auth::getUser();
                if ($user) {
                    View::renderTemplate('/Ideas/show.html', [
                        'idea' => $idea,
                        'can_vote' => $user->canVote($idea->id),
                        'vote_rate' => $user->ideaVoteRate($idea->id)
                    ]);
                } else {
                    View::renderTemplate('/Ideas/show.html', [
                        'idea' => $idea
                    ]);
                }
            } else {
                Flash::addMessage('Pomysł nie istnieje!', FLASH::WARNING);
                $this->redirect('/ideas');
            }
        } else {
            $this->redirect('/ideas');
        }
    }


    public function createAction()
    {
        $categories = IdeasCategories::getAll();

        $user = Auth::getUser();


        $idea = new Idea($_POST);

        $validator = new Validator($idea);

        $validator->add(new Form('Nazwa pomysłu', 'name', [
            'maxlength' => 120,
            'minlength' => 10,
            'notExistDb' => 'ideas/name'
        ]));

        $validator->add(new Form('Opis', 'content', [
            'minlength' => 10
        ]));

        $validator->add($password = new Form('Wkład', 'contribution', [
            'number' => true
        ]));

        $validator->add(new Form('Kategoria', 'category', [
            'existDb' => 'ideas_categories/id'
        ]));


        if ($idea->save($validator, $user->id)) {
            Flash::addMessage('Pomyślnie dodano. Pomysł zostanie wyświetlony po akceptacji przez administratora.');
            $this->redirect('/ideas/show/');
        } else {
            View::renderTemplate('/Ideas/add.html', [
                'idea' => $idea,
                'categories' => $categories,
                'token_form' => Input::generateFormToken()
            ]);
        }
    }


    public function addAction()
    {
        $categories = IdeasCategories::getAll();
        View::renderTemplate('/Ideas/add.html', [
            'categories' => $categories,
            'token_form' => Input::generateFormToken()
        ]);
    }

}