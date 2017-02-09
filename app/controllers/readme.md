# Les controlleurs
Ils sont tous des enfants de DBInterface pour permettre de ne pas avoir à refaire la connexion à chaque fois.
Dans les constructeurs de nos controlleurs ont doit récupérer le constructeur parent avec la ligne : 

    function __construct() { 
        parent::__construct();
    
        /* .. */
    }
    
## Dénomination
Les controllers se doivent de respecter une dénomination précise.
Les fichiers doivent se nommer : **<Nomdelaclass>Controller.php**
Les classes controllers doivent toutes avoir le suffixe **Controller**

**Exemple** pour *Users*
```
    UsersController.php
    class UsersController { }
```
