<?php
/**
 * Created by PhpStorm.
 * User: tobirick
 * Date: 13.11.17
 * Time: 19:22
 */

namespace Core;

class Router {

    protected $routes = [];
    protected $params = [];

    public function add($route, $params = []) {

        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function match($url) {
        /*foreach ($this->routes as $route => $params) {
            if ($url == $route) {
                $this->params = $params;
                return true;
            }
        }
        */

        //$reg_exp = "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/";

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Get named capture group values
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

    public function getParams() {
        return $this->params;
    }

    public function dispatch($url) {

        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            //$controller = "App\Controllers\\$controller";
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                // check if there is a action, if not index is default action
                if (array_key_exists('action', $this->params)) {
                    $action = $this->params['action'];
                } else {
                    $action = 'index';
                }
                $action = $this->convertToCamelCase($action);

                if(preg_match('/action$/i', $action) == 0) {
                    $controller_object->$action();
                } else {
                    //echo "Method $action (in controller $controller) not found";
                    throw new \Exception("Method $action (in controller $controller) not found", 404);
                }
            } else {
                //echo "controller class $controller not found";
                throw new \Exception("controller class $controller not found", 404);
            }
        } else {
            //echo "no route matched";
            throw new \Exception("no route matched", 404);
        }
    }

    public function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    public function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

	// &id=5 for example
    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

	// get namespace of controllers folder
    protected function getNamespace() {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}