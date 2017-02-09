<?php
/**
 * FrontController rredirect calls to the specified controller
 */
class FrontController
{
    /**
     * Set default values
     */
    protected $controller  = "IndexController";
    protected $action      = "index";
    protected $params      = [];

    /**
     * Set trigger path
     */
    protected $basePath    = "do/";

    /**
     * Init the FrontController
     *
     * @param $args array
     */
    public function __construct(array $args = NULL)
    {
        if($args == NULL)
        {
            $this->parseURI();
        }
        else { }
    }

    /**
     * Parse the URI and set $controller, $action, $params acordingly
     */
    protected function parseURI()
    {
        //Clean up URL
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        //Remove basepath
        if(strpos($path, $this->basePath) == 0)
        {
            $path = substr($path, strlen($this->basePath));
        }

        //Get all given variables
        @list($controller, $action, $params) = explode("/", $path, 3);

        if(isset($controller))
            $this->setController($controller);

        if(isset($action))
            $this->setAction($action);

        if(isset($params))
            $this->setParams(explode("/", $params));
    }

    protected function setController($controller)
    {
        $controller .= "Controller";

        if(class_exists($controller))
            $this->controller = $controller;
        else throw new InvalidArgumentException("The controller ".$controller." could not be found.");
    }

    protected function setAction($action)
    {
        $reflector = new reflectionclass($this->controller);

        if($reflector->hasMethod($action))
            $this->action = $action;
        else throw new InvalidArgumentException("The action ".$action." is not a method of the ".$this->controller.".");
    }

    protected function setParams(array $params)
    {
        $this->params = $params;
    }

    public function run()
    {
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
}
