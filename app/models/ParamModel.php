<?php

class ParamModel extends DBInterface
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Insert defaults parameters in database if missing
     */
    public function checkDatabase()
    {
        ///////////////////////////

        //ADD NEW PARAMETERS HERE
        $defaultsParams = ["USER_MAX_PROFILES" => 3];

        ///////////////////////////


        $stmt = $this->cnx->prepare("SELECT COUNT(PARAM_NAME) FROM PARAMS");
        $stmt->execute();

        $nbrParams = $stmt->fetchColumn();

        if($nbrParams >= count($defaultsParams))
            return;

        $insertStmt = $this->cnx->prepare("INSERT INTO PARAMS(PARAM_NAME, PARAM_VALUE, PARAM_edit_time) VALUES(:name, :value, :time)");

        foreach($defaultsParams as $pName => $pValue)
        {
            $insertStmt->execute([":name" => $pName,
                                  ":value" => $pValue
                                  ":time" => 0]);
        }
    }




    /**
     * Return the list of all the parameters
     * @return array All the parameters
     */
    public function getAll()
    {
        $stmt = $this->cnx->prepare("SELECT PARAM_NAME, PARAM_VALUE, PARAM_edit_time AS LAST_EDIT, PARAM_edit_user_id AS USER_ID FROM PARAMS ORDER BY PARAM_NAME");
        $stmt->execute();

        $params = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //CLose connexion
        $stmt = null;
        $this->cnx = null;

        return $params;
    }



    /**
     * Update a parameter with the given value
     * @param  string  $paramName  Name of the param to update
     * @param  mixed   $paramValue New value for the parameter
     * @return boolean true on success
     */
    public function update($paramName, $paramValue)
    {
        $stmt = $this->cnx->prepare("UPDATE PARAMS SET PARAM_VALUE = :value, PARAM_edit_time = :time, PARAM_edit_user_id = :user WHERE PARAM_NAME = :name");
        $stmt->execute([":value" => $paramValue,
                        ":time" => time(),
                        ":user" => Session::read("userID"),
                        ":name" => $paramName]);

        return true;
    }
}
