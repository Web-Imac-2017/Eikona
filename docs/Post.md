
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





## Affichage des informations d'un post

Créer un post pour l'utilisateur courant.

### URL
```
/post/display/<postID>
```

### Méthode
**GET**

### Variables POST

  * **postID** : ID du post

### Succès

  * **Code:** 200 OK <br />
Data: 
```json
{
  postID : ID du post,
  profileID : ID du profil,
  desc : Description du post,
  publishTime : Date de publication,
  updateTime : Date de la modification,
  allowComments : 1 = comments enabled, 0 = comments disabled,
  approved : Le post a été approuvé,
  state : Etat de modération du post,
  geo : {
    lat : latitude,
    lng : longitude, 
    name : Nom du lieu
  }
}
```

 
### Erreurs
 
  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **postID** n'est pas un ID

  OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Le profil est privé est le user ne le suit pas


## Mise à jour d'un post

Met à jour le champ indiqué du post

### URL
```
/post/update/<field>/<postID>
```

### Méthode
**POST**

### Variable GET

  * **field** : Nom du champ à modifier `DESCRIPTION|GEO|ALLOWCOMMENTS|DISABLECOMMENTS|POSTAPPROVED|STATE`
    **postID** : ID du post à utiliser

### Variable POST

  **Variable Optionnel**
  
  * **desc** : Nouvelle description du post
    **post_geo_lat** : Nouvelle latitude du post
    **post_geo_lng** : Nouvelle longitude du post
    **post_geo_name** : Nouveau name de la géo du post
	  **allowComments** : Permet d'accepter les commentaires
  	**disableComments** : Permet d'empêcher les commentaires
	  **postApproved** : Permet de passer un post à Approved
    **state** : Le nouveau state du post

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    profileID : ID du profil, 
    profileName OU profileDesc OU profileIsPrivate OU state : Valeur mise à jour
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **postID** n'est pas un ID OU une variable POST est absente

  OU

  * **Code:** 405 METHOD NOT ALLOWED <br />
    **Explication** Le field spécifié n'est pas supporté.
	
  OU

  * **Code:** 400 MISSING VALUE <br />
    **Explication** Il manque la nouvelle valeur du field.

     OU

  * **Code:** 400 MISSING VALUE <br />
    **Explication** Mauvaise valeur pour le state
  

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

## Liker un post

Like un post avec le profil courant

### URL
```
/post/like/<postID>/
```

### Méthode
**GET**

### Variable GET

  * **postID** : ID du post à liker

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    postID : ID du post, 
    profileID: ID du profil qui vient de like le post
}
```

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Le post spécifié n'existe pas OU l'user n'a pas de profil courant OU vous ne suivez pas la personne

  OU

  * **Code:** 400 NOT AUTHORIZED <br />
    **Explication** On ne peut pas aimer son propre post OU Le post a déjà été aimé par le profil courant

## dé-Liker un post

dé-Like un post avec le profil courant

### URL
```
/post/unlike/<postID>/
```

### Méthode
**GET**

### Variable GET

  * **postID** : ID du post à dé-liker

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    postID : ID du post, 
    profileID: ID du profil qui vient de dé-like le post
}
```

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Le post spécifié n'existe pas 'user n'a pas de profil courant

  OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Le post n'a pas été aimé

## Récupérer tous les likes d'un post

Récupère et liste les likes d'un post

### URL
```
/post/likes/<postID>/
```

### Méthode
**GET**

### Variable GET

  * **postID** : ID du post à dé-liker

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    postID : ID du post, 
    nbOfLikes: nombres de likes du post (si 0, pas de likes),
    like:{
      profile_id : ID du profil qui a like le post,
      profile_name : Le nom du profil,
      like_time : Date du like
    }
}
```

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Le post spécifié n'existe pas 'user n'a pas de profil courant OU vous ne suivez pas le profil

  OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Le post n'a pas été aimé
	
## Récupérer les commentaires d'un post

Récupère tous les commentaires d'nu post

### URL
```
/post/comments/<postID>/
```

### Méthode
**GET**

### Variable GET

  * **postID** : ID du post dont il faut récupérer les commentaires

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    postID : ID du post, 
    nbOfComments: nombre de commentaires,
    comments : {
      comment_id : ID du commentaire,
      profile_id : ID du profil qui a commenté,
      profile_name : Nom du profil qui a commenté,
      comment_texte : Le corps du commentaire,
      comment_time : Date du commentaire
    }
}
```

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Le post spécifié n'existe pas OU l'user n'a pas de profil courant OU vous ne suivez pas la personne

  OU

  * **Code:** 400 NOT AUTHORIZED <br />
    **Explication** On ne peut pas aimer son propre post OU Le post a déjà été aimé par le profil courant

