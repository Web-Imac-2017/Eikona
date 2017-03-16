<?php

/**
 * Handle database connection
 */
class DBInterface
{
	protected $cnx;

	/**
	 * Call at the beginning of every model, initiate the connnection to the database
	 */
	public function __construct()
    {
		$this->cnx = new PDO("mysql:dbname=roger;host=localhost;charset=utf8", "root", "root") or die("connexion à la bdd impossible");

        //Display PDO errors
        $this->cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        //Use UTF-8 char-set to ensure full support of special characters. I'm looking at you, emojis.
        $this->cnx->exec("SET CHARACTER SET utf8");
	}




    /**
     * Hnadle call to an undefined model method.``
     *
     * TODO: Deactivate in production because it exposes internal methods.
     *
     * @private
     * @param  string  $method Method originaly called
     * @param  array   $args   Args passed to the variable
     * @return boolean return false to end with an error
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
