<?php
/**
 * FrontController rredirect calls to the specified controller
 */
class FrontController
{
    /**
     * Set default values
     */
    protected $controllers = "index";
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

    protected function parseURI()
    {
    }
}
