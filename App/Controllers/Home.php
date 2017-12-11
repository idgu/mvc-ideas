<?php
/**
 * User: idgu
 * Date: 18.11.2017
 * Time: 19:21
 */

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Idea;

class Home extends \Core\Controller
{

    public function indexAction() {
        $ideas = Idea::getAll('add_date/desc/'. 0 .', 6', true );

        View::renderTemplate('/Home/index.html', [
            'ideas' => $ideas
        ]);
    }
}