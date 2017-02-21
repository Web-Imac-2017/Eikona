# Profils

Les profils sont gérés par le **profilesController**.
Celui-ci permet d'accèder à toutes les informations relatives à ces derniers.

## Création d'un profil

Créer un profil pour l'utilisateur courant.

### URL
```
/profiles/create/
```

### Méthode
```
POST
```

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

## Nom d'un profil

Récupère le nom d'un profil

### URL
```
/profiles/name/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** 
    
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
/profiles/description/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileDesc : Description du profil
    }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas


## Description d'un profil

Récupère l'URL de l'image d'un profil

### URL
```
/profiles/picture/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profilePicture : URL de l'image du profil
    }`
 
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
/profiles/views/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileViews : Nombre de vues du profil
    }`
 
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
/profiles/isprivate/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileIsPrivate : Indique si le profile est privé (true/false)
    }`
 
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
/profiles/owner/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileOwner : UserID du propriétaire du profil
    }`
 
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
/profiles/update/<field>/<profileID>
```

### Méthode
```
POST
```

### Variable GET

  * **field** : Nom du champ a modifier `NAME|DESCRIPTION|SETPRIVATE|SETPUBLIC`
  * **profileID** : ID du profil à utiliser

### Variable POST

  **Variable Optionnel**
  
  * **newValue** : Utilisée pour mettre à jour le nom et la description de profil. 

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileName OU profileDesc OU profileIsPrivate : Valeur mise à jour
    }`
 
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
/profiles/setPicture/<profileID>
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser

### Variable FILE

  * **profilePicture** : Image à utiliser

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profilePicture : URL de la nouvelle image de profil
    }`
 
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
/profiles/ddView/<profileID>/[<nbrView>/]
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à utiliser
  
  **Variable optionnelle**
  
  * **nbrView** : Le nombre de vues à ajouter au profil, une par défaut.

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ 
    profileID : ID du profil, 
    profileViews : Nombre de vues du profil
    }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas

## supprimer un profil

Ajoute une ou plusieurs vues au profil

### URL
```
/profiles/delete/<profileID>/
```

### Méthode
```
GET
```

### Variable GET

  * **profileID** : ID du profil à supprimer

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{}`

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à supprimer à jour ce profil

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas
