<?php
/**
 * User: idgu
 * Date: 19.11.2017
 * Time: 12:50
 */

namespace Core;


class View
{
    public static function render($view, $args =[])
    {
        extract($args, EXTR_SKIP);
        $file = "../App/Views$view";

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file is not found.");
        }
    }



    public static function renderTemplate($template, $args = [])
    {
        echo self::getTemplate($template, $args);
    }



    public static function getTemplate($template, $args = [])
    {
        static $twig = null;
        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem('../App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('URL', \App\Config::URL);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
    }

        return $twig->render($template, $args);
    }
}