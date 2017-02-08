# Les controlleurs
Ils sont tous des enfants de DBInterface pour permettre de ne pas avoir à refaire la connexion à chaque fois.
Dans les constructeurs de nos controlleurs ont doit récupérer le constructeur parent avec la ligne : 

    function __construct() { 
        parent::__construct();
    
        /* .. */
    }
    

