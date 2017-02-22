
# Les Posts

Les posts sont gérés par le **postController**.
Celui-ci permet d'accéder à toutes les informations relatives à ces derniers.

## Création d'un post

Créer un post pour l'utilisateur courant.

### URL
```
/post/create/
```

### Méthode
**POST**

### Variables POST

  * **img** : Image du post
  
  **Variables optionnelles**
  
  * **postDescription** : Description du post
  * **postType** : A définir pour le moment jusqu'à ce que ça soit mis en place

### Succès

  * **Code:** 201 CREATED <br />
    **Data:** `{ profileID : ID du profil créé }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />

## Supprimer un post

Supprime un post du profil courant ainsi que la photo du dossier medias/img/

### URL
```
/profile/delete/<postID>/
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du post à supprimer

### Succès

  * **Code:** 200 OK

### Erreurs

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le post spécifié n'existe pas


## Informations d'un post

Récupère toutes les informations d'un profil

### URL
```
/profile/get/<postID>
```

### Méthode
**GET**

### Variable GET

  * **postID** : ID du post à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{
    postID: ID du post,
	// A définir ce dont vous avez besoin
}
```

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


## Mise à jour d'un post

Met à jour le champ indiqué du post

### URL
```
/profile/update/<field>/<profileID>
```

### Méthode
**POST**

### Variable GET

  * **field** : Nom du champ a modifier `DESCRIPTION|GEO|ALLOWCOMMENTS|DISABLECOMMENTS|POSTAPPROVED`
  * **profileID** : ID du profil à utiliser

### Variable POST

  **Variable Optionnel**
  
  * **desc** : Nouvelle description du post
    **post_geo_lat** : Nouvelle latitude du post
    **post_geo_lng** : Nouvelle longitude du post
    **post_geo_name** : Nouveau name de la géo du post
	**allowComments** : Permet d'accepter les commentaires
	**disableComments** : Permet d'empêcher les commentaires
	**postApproved** : Permet de passer un post à Approved

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
    **Explication** La variable GET **postID** n'est pas un ID OU une variable POST est absente

  OU

  * **Code:** 405 METHOD NOT ALLOWED <br />
    **Explication** Le field spécifié n'est pas supporté.
