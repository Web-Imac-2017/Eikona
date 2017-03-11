<?php

class DBInterface{

	protected $cnx;

	public function __construct(){
		$this->cnx = new PDO("mysql:dbname=roger;host=localhost;charset=utf8", "root", "root") or die("connexion à la bdd impossible");
        $this->cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->cnx->exec("SET CHARACTER SET utf8");
	}




    /**
     * Hnadle call to an undefined method.
     */
    public function __call($method, $args)
    {
        echo "Unknown method <strong>".$method."</strong><br>";
        echo "Supported methods :<br><ul>";

        $class_methods = get_class_methods(get_called_class());

        foreach ($class_methods as $method_name)
        {
            echo "<li>".$method_name."</li>";
        }

        echo "</ul>";

        return false;
    }
}
