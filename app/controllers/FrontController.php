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
    protected $basePath    = "eikona/do/";

    /**
     * Init the FrontController
     *
     * @param $args array Must respect this format to work : ["controller" => "", "action" => "", "params" => []]
     */
    public function __construct(array $args = [])
    {
        if(empty($args))
            $this->parseURI();
        else
        {
            if(isset($options['controller']))
                $this->setController($options['controller']);

            if(isset($options['action']))
                $this->setAction($options['action']);

            if(isset($options['params']))
                $this->setParams($options['params']);
        }
    }

    /**
     * Parse the URI and set $controller, $action, $params acordingly
     */
    protected function parseURI()
    {
        //Clean up URL
        $path = trim(parse_url(strtolower($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/');
        //Remove basepath
        if(strpos($path, $this->basePath) === 0)
        {
            $path = substr($path, strlen($this->basePath));
        }

        //Get all given variables
        @list($controller, $action, $params) = explode("/", $path, 3);

        //Assing new values
        if(isset($controller))
            $this->setController($controller);

        if(isset($action))
            $this->setAction($action);

        if(isset($params))
            $this->setParams(explode("/", $params));
    }
    
    /**
     * Set which controller to call, and confirm it exists
     */
    protected function setController($controller)
    {
        $controller .= "Controller";

        if(class_exists($controller))
            $this->controller = $controller;
        else throw new InvalidArgumentException("The controller ".$controller." could not be found.");

        return $this;
    }
    
    /**
     * Set which method to call, and confirm it exists
     */
    protected function setAction($action)
    {
        $reflector = new reflectionclass($this->controller);

        if($reflector->hasMethod($action))
            $this->action = $action;
        else throw new InvalidArgumentException("The action ".$action." is not a method of the ".$this->controller.".");

        return $this;
    }

    
    /**
     * Add params to the list.
     */
    protected function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
    
    /**
     * Execute the request
     */
    public function run()
    {
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
}
