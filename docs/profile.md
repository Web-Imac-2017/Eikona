
# Les Profils

Les profils sont gérés par le **profileController**.
Celui-ci permet d'accèder à toutes les informations relatives à ces derniers.






## Création d'un profil

Créer un profil pour l'utilisateur courant.

### URL
```
/profile/create/
```

### Méthode
**POST**

### Variables POST

  * **profileName** : Nom du profil
  
  **Variables optionnelles**
  
  * **profileDesc** : Description du profil
  * **profilePrivate** : À transmettre si le profil est privé

### Succès

  * **Code:** 201 CREATED <br />
    **Data:** `{ profileID : ID du profil créé }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST **profileName** n'a pas été transmise.

  OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Le nom de profil est déjà utilisé.

### Notes

  Un profil ne peut être créer si il n'y a pas d'utilisateur de connecté.






## Choix d'un profil

Utilisé pour définir le profil courant utilisé par l'utilisateur

### URL
```
/profile/setCurrent/<profileID>
```

### Méthode
**GET**

### Variables POST

  * **profileID** : ID du profil à définir comme courant

### Succès

  * **Code:** 20O OK <br />
    **Data:** `{ profileID : ID du profil choisi }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** nn'est pas correcte

  OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** L'utilisateur ne peut pas utiliser ce profile, peut-être qu'il ne lui appartient pas.
    





## Informations d'un profil

Récupère toutes les informations d'un profil

### URL
```
/profile/get/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID: ID du profil,
    ownerID: ID du user propriétaire du profil,
    profileName: Nom du profil,
    profileDesc: Description du profil,
    profileCreateTime: Timestamp de la création du profil,
    profileViews: Nombre de vues du profil,
    profileIsPrivate: Confidentialité du profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas






## Nom d'un profil

Récupère le nom d'un profil

### URL
```
/profile/name/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileName : Nom du profil 
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas






## Description d'un profil

Récupère la description d'un profil

### URL
```
/profile/description/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileDesc : Description du profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas






## Vues d'un profil

Récupère le nombre de fois ou le profil a été vu.

### URL
```
/profile/views/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileViews : Nombre de vues du profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas







## Confidentialité d'un profil

Récupère le paramètre de confidentialité (Privé/Publique) du profil.

### URL
```
/profile/isprivate/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileIsPrivate : Indique si le profile est privé (true/false)
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas






## Propriétaire du profil

Récupère l'identifiant de l'utilisateur propriétaire du profil

### URL
```
/profile/owner/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileOwner : UserID du propriétaire du profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas







## Posts du profil

Récupère les posts du profil spécifié selon les arguments spécifiés

### URL
```
/profile/posts/<profileID>[/after/<timestamp>][/before/<timestamp>][/<limite>[/<offset>]][/<order>]
```

**Note** Les crochets indiquent une valeur optionnelle. L'ordre des paramètres entre croches n'a pas d'importance.

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser
  * after **timestamp** : Date a partir de laquelle récupérer des posts (exclue)
  * before **timestamp** : Date jusqu'a laquelle récuperer des posts (exclue)
  * **limite** : Nombre maximal de posts à récuperer. 4096 par défaut
  * **offset** : Nombre de posts de décalage pour la recherche
  * **order** : Sélectionner les posts en commencant par le plus ancien (asc) ou par le plus récent (desc, par défaut) 

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    nbrPosts : Nombre de posts trouvés, 
    posts : Tableau avec les ID de touts les posts trouvés
}
```
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas







## Mise à jour du profil

Met à jour le champ indiqué du profil

### URL
```
/profile/update/<field>/<profileID>
```

### Méthode
**POST**

### Variable GET

  * **field** : Nom du champ a modifier `NAME|DESCRIPTION|SETPRIVATE|SETPUBLIC`
  * **profileID** : ID du profil à utiliser

### Variable POST

  **Variable Optionnel**
  
  * **newValue** : Utilisée pour mettre à jour le nom et la description de profil. 

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileName OU profileDesc OU profileIsPrivate : Valeur mise à jour
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU La variable POST newValue est absente

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à mettre à jour ce profil

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas
  
  OU
 
   * **Code:** 405 METHOD NOT ALLOWED <br />
     **Explication** Le field spécifié n'est pas supporté.






## Modifier l'image du profil

Remplace l'image actuelle du profil par une nouvelle

### URL
```
/profile/setPicture/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Variable FILE

  * **profilePicture** : Image à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profilePicture : URL de la nouvelle image de profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à mettre à jour ce profil

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas

  OU

  * **Code:** 406 NOT ACCEPTABLE <br />
    **Explication** Le format de l'image transmise n'est pas supporté






## Ajouter des vues au profil

Ajoute une ou plusieurs vues au profil

### URL
```
/profile/ddView/<profileID>/[<nbrView>/]
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser
  
  **Variable optionnelle**
  
  * **nbrView** : Le nombre de vues à ajouter au profil, une par défaut.

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileViews : Nombre de vues du profil
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas






## Supprimer un profil

Ajoute une ou plusieurs vues au profil

### URL
```
/profile/delete/<profileID>/
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à supprimer

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à supprimer à jour ce profil

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas
