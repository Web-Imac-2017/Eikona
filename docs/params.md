# Les Parmètres

Les paramètres du site permet de stocker des inforamtions utilisées pour le fonctionnement du site mais suceptible de changer avec le temps.
L'ajout d'un paramètre se fait au cas par cas, directement dans la base de donnée.

Les méthodes suivantes nécessite d'être connecté en tant qu'administrateur


## Récupération de tous les paramètres

Retourne la liste de tous les paramètres et leur valeurs

### URL
```
do/param/getall
```

### Méthode
**GET**

### Succès

  * **Code:** 200 OK <br />
  * **Data:**
 ```
 {
  nbrParams: Nombre de paramètre retournés
  params: Tableau contenant tous les paramètres. Sur le format:
      {
        PARAM_NAME: Nom du paramètre
        PARAM_VALUE: Valeur du paramètre
        LAST_EDIT: Timestamp de dernière édition (0 si jamais édité)
        USER_ID: ID de l'utilisateur ayant fait la dernière modification (NULL si jamais édité)
      }
 }
 ```
 
### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** L'utilisateur courant n'est pas un administrateur
    
    
    


## Mise à jour d'un paramètre

Met à jour un paramètre

### URL
```
do/param/update/<paramName>
```

### Méthode
**GET**

### Variable GET

  * **paramName** : Nom du paramètre à modifier
  
### Variable POST
  
  * **PARAM_VALUE** : Nouvel valeur à affecter au paramètre
  
### Succès

  * **Code:** 200 OK <br />
  * **Data:**
 ``` { } ```
 
### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** L'utilisateur courant n'est pas un administrateur

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST **PARAM_VALUE** est manquante.

