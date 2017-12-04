<?php

namespace Core;
/**
 * User: idgu
 * Date: 18.11.2017
 * Time: 16:08
 */


/**
 * Class responsible for analize query string, check if is correct and invoke
 * controllers and methods with params or throw an error.
 *
 * Class Router
 * @package Core
 */
class Router
{

    /**
     *Regular expressions which query string must validate
     *
     * @var array
     */
    protected $routes = array();


    /**
     * Params send to method in chosen controller
     *
     * @var array
     */
    protected $params = array();


    /**
     *
     * This method transfrom $route into regular expression(.*), (.*)
     * and write them in the table key $this->routes which optional contains $params (controler, method)
     *
     * -------------------------------------------------
     * IMPORTANT!!!!!!
     *
     * YOU CAN NOT USE {0,5} in your $route~~~!!!
     * -------------------------------------------------
     *
     * Example
     * ------------
     * $route = new Router();
     * $route -> add('{controller}/{action}/{userid:\d+}');
     * $route -> add('{controller}/{action}');
     * $route -> add('{controller}/{action}/{userid:\d+}');
     * $router->add('', ['controller'=> 'Home', 'action'=> 'index']);
     * $router->add('password/reset/{token:[\da-f]+}', ['controller'=> 'Password', 'action'=> 'reset']);
     * ------------
     *
     *
     * route -> add('{controller}/{action}/{userid:\d+}');
     *
     * Change given string to:
     * "^(?<controller>[a-z-]+)\/(?<action>[a-z-]+)\/(?<userid>\d+)$/i"
     *
     * @param $route
     * @param array $params  Contain controller and method name
     */
    public function add($route, $params = array())
    {
        $route = preg_replace("/\//", "\\/", $route);
        $route = preg_replace('/{([a-z-]+)}/', '(?<\\1>[a-z-]+)', $route);
        $route = preg_replace("/{([a-z]+):/", "(?<$1>", $route);
        $route = preg_replace("/}/", ")", $route);
        $route = '/^'. $route . '$/i';


        $this->routes[$route] = $params;

    }
//    public function add($route, $params = array())
//    {
//        $route = preg_replace("/\//", "\\/", $route);
//        echo htmlspecialchars($route) . '<br>';
//        $route = preg_replace('/{([a-z-]+)}/', '(?<\\1>[a-z-]+)', $route);
//        echo htmlspecialchars($route) . '<br>';
//
//        $route = preg_replace("/{(\w+):(.*)}/", "(?<$1>$2)", $route);
//        echo htmlspecialchars($route) . '<br>';
//
//        $route = '/^'. $route . '$/i';
//        die();
//        $this->routes[$route] = $params;
//    }



    public function getRoutes()
    {
        return $this->routes;
    }



    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url ='';
            }
        }
        return $url;
    }


    /**
     * Check if $url matches to regular ezpressions
     *
     * @param $url
     * @return bool
     */
    private function match($url)
    {

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }



    /**
     * Analize QUERY STRING, if matches to regular expression in $this->routes keys, then
     * create new instance of controller and invoke methon taken from $this->routes (body).
     *
     *
     * @param $url
     * @throws \Exception
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);
        $url = rtrim($url, '/');

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action ])) {
                    $controller_object -> $action();
                } else {
                    throw new \Exception("Metod $action (in controller $controller) not found", 404);
                }
            } else {
                throw new \Exception("Controller class $controller not found", 404);
            }
        } else {
            throw new \Exception( "No route matched", 404);
        }

    }


    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }


    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }


    public function getParams()
    {
        return $this->params;
    }


    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace']. '\\';
        }

        return $namespace;
    }
}