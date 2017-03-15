<?php

interface ParamControllerInterface
{
    public function __get($param);
    
    public function init();
    
    public function getAll();
    
    public function update($paramName);
}
}

class ParamController implements ParamControllerInterface
{
    private $model;
    private $params = null;

    public function __construct()
    {
        $this->model = new ParamModel();

        $this->model->checkDatabase();
    }

    /**
     * Handle call to parameters
     * @param  string $param Name of the parameter to retreive
     * @return mixed  False if the parameter is not found, the parameter value otherwise
     */
    public function __get($param)
    {
        if($this->params === null)
        {
            $this->init();
        }

        if(!array_key_exists($param, $this->params))
            return false;

        return $this->params[$param];
    }

    /**
     * Collect all parameters and store them in the class
     */
    public function init()
    {
        $rsp = $this->model->getAll();

        $params = array();

        foreach($rsp as $param)
        {
            $params[$param["PARAM_NAME"]] = $param["PARAM_VALUE"];
        }

        $this->params = $params;
    }

    /**
     * Return all the parameters
     */
    public function getAll()
    {
        $rsp = new Response();

        if(!isAuthorized::isAdmin())
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        $params = $this->model->getAll();

        $rsp->setSuccess(200)
            ->bindValue("nbrParams", count($params))
            ->bindValue("PARAMS", $params)
            ->send();
    }

    /**
     * Update the given parameter
     * @param string $paramName Name of the parameter to update
     */
    public function update($paramName)
    {
        $rsp = new Response();

        if(!isAuthorized::isAdmin())
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!isset($_POST["PARAM_VALUE"]))
        {
            $rsp->setFailure(400, "Missing param new value.")
                ->send();

            return;
        }

        $this->model->update(strtoupper($paramName), $_POST["PARAM_VALUE"]);

        $rsp->setSuccess(200)
            ->send();
    }
}
