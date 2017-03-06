
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
    **Data:** `{

	postId: Id du post,
	profileID: ID du profil du post en question
	desc: Description du post
	publishTime: Moment où le post a été publié
	allowComments: Si le post autorise les commentaires
	approved: Si le post est approuvé
	updateTime: Moment où le post a été mis à jour
	state: Etat du post
	geo: Nom, latitude et longitude du post
	}`

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **postID** n'est pas un ID

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le post spécifié n'existe pas





## Récupère une info d'un post

Récupère une information passée en paramètre d'un post

### URL
```
/post/<field>/<postID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser
    **field** : Nom du champ à récupérer `DESCRIPTION|GEO|PUBLISHTIME|STATE|ALLOWCOMMENTS|APPROVED|UPDATETIME`

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID : ID du profil,
    <field> : Informations du field
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
/post/update/<field>/<postID>
```

### Méthode
**POST**

### Variable GET

  * **field** : Nom du champ à modifier `DESCRIPTION|GEO|ALLOWCOMMENTS|DISABLECOMMENTS|POSTAPPROVED`
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

  OU

  * **Code:** 400 MISSING VALUE <br />
    **Explication** Il manque la nouvelle valeur du field.




## Mise à jour du state d'un post

Met à jour l'état d'un post

### URL
```
/post/updateState/<postID>
```

### Méthode
**POST**

### Variable GET

  * **postID** : ID du post à utiliser

### Variable POST

  * **state** : Nouvel état du post

### Succès

  * **Code:** 200 OK
Data:
```json
{
    postID : ID du post,
    state: Etat mis à jour
}
```

### Erreurs

  * **Code:** 400 FAILURE <br />

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
    **Explication** Le post spécifié n'existe pas OU l'user n'a pas de profil courant

  OU

  * **Code:** 400 NOT AUTHORIZED <br />
    **Explication** Le post est privé OU on ne peut pas aimer son propre post OU Le post a déjà été aimé par le profil courant

  OU

  * **Code:** 406 NOT ACCEPTABLE <br />
    **Explication** Le profil courant a liké plus de 200 post durant les 60 dernières minutes. (Securité Anti-Bot)

## Dé-Liker un post

Dé-Like un post avec le profil courant

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

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Le post spécifié n'existe pas 'user n'a pas de profil courant

  OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Le post n'a pas été aimé
