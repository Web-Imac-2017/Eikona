
# Les Tags

Les tags sont gérés par le **TagController**.





## Création d'un tag

Créer un tag pour l'utilisateur courant sur le post en question.

### URL
```
do/tag/add/{{postID}}/<tagName>
```

### Méthode
**GET**

### Variables GET

  * **postID** : ID du post où ajouter le tag 
    **tagName** : Nom du tag

### Succès

  * **Code:** 201 TAG ADDED <br />
 
### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Pas l'autorisation de modifier le post : ID du profil différent de l'ID profile du post.

  OU
  
    * **Code:** 401 NOT SELECTED <br />
    **Explication** Pas de profil sélectionné.

  OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Le tag pour ce post existe déjà.

### Notes

  Un tag ne peut être ajouter que s'il n'existe pas déjà et seulement si l'ID du Profil de la session correspond à l'id du Profil du post.



## Supprimer un tag

Supprime un tag sous un post donné.

### URL
```
do/tag/delete/{{postID}}/<tagName>
```

### Méthode
**GET**

### Variable GET

  * **tagName** : Nom du tag à supprimer
    **postID** : ID du post sous lequel est présent le tag

### Succès

  * **Code:** 200 TAG DELETED

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Pas l'autorisation de modifier le post : ID du profil différent de l'ID profile du post.

  OU
  
    * **Code:** 401 NOT SELECTED <br />
    **Explication** Pas de profil sélectionné.

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le tag pour ce post n'existe pas.


## Count tag

Compter le nombre de fois où un tag est utilisé 

### URL
```
do/tag/delete/<tagName>
```

### Méthode
**GET**

### Variable GET

  * **tagName** : Nom du tag à rechercher

### Succès

  * **Code:** 200 OK
Data:
```json
{
    nbTag: Nb de fois où le tag est utilisé
}
```

