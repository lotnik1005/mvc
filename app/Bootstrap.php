<?php
class Bootstrap{
    private $controller;
    private $action;
    private $argument;
    private $request;

    public function __construct($request){
        $this->request = str_replace(ROOT_DOMAIN, "/", $request);
        $this->action = 'index';
        $this->argument = '';
        $this->processRequest();
    }

    public function createController(){
        // Check Class
        if(is_object($this->controller)){
            $parents = class_parents($this->controller);
            // Check Extend
            if(in_array("Controller", $parents)){
                if(method_exists($this->controller, $this->action)){
                    return $this->controller;
                } else {
                    // method not exist
                    echo '<h1>Metoda nie istnieje</h1>';
                    return;
                }
            } else {
                // class not extends class controller
                echo '<h1>Klasa nie rozszerza klasy Controller</h1>';
                return;
            }
        } else {
            // class controller not found
            echo '<h1>Nie udało się utworzyć obiektu</h1>';
            return;
        }
    }

    private function processRequest() {
        if ($this->request == '/') {
            $this->controller = new HomeController($this->action, $this->argument);
            return;
        }

        $uriExploded = explode("?", $this->request);
        if (count($uriExploded) < 2) {
            $controllerUri = $this->request;
        }
        else {
            // query string in place
            $controllerUri = $uriExploded[0];
            $queryString = $uriExploded[1];
        }

        $components = explode("/", $controllerUri);
        $componentsCount = count($components);

        try {
            $controllerName = ucfirst(strtolower($components[1]));
            $controllerClass = $controllerName . "Controller";
            if (!class_exists($controllerClass))
                throw new Exception("Nie znaleziono klasy kontrolera.");

            switch ($componentsCount) {
                case 2:
                    // host www + controller
                    $this->action = 'index';
                    break;
                case 3:
                    // host www + controller + method
                    $this->action = $components[2];
                    break;
                case 4:
                    // host www + controller + method + argument
                    $this->action = $components[2];
                    $this->argument = $components[3];
                    break;
                default:
                    // error URL adress
                    throw new Exception("Błedny adres URL");
            }

            $this->controller = new $controllerClass($this->action, $this->argument);
        }
        catch (Exception $e) {
            $this->action = 'error';
            $this->argument = $e->getMessage();
            $this->controller = new HomeController($this->action, $this->argument);
        }
    }
}