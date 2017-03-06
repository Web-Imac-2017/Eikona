
# Les Commentaires

Les posts sont gérés par le **CommentController**.
Celui-ci permet d'accéder à toutes les informations relatives à ces derniers.

## Création d'un commentaire

Ajoute un nouveau commentaire à un post

### URL
```
/comment/create/<postID>
```

### Méthode

**GET**

### Variables GET

* **postID** : ID du post à commenter

### Variables POST

* **commentText** : Corps du commentaire

### Succès

  * **Code:** 200
Data:
```json
{
  userID: id du user,
  profileID : id du profil,
  postID : id du post commenté,
  comment : Texte du commentaire
}
```

### Erreurs

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id du post ne renvoie à aucun post

  OU 

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise OU Les commentaires sont désactivés pour ce post

  OU

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** Vous ne suivez pas la personne OU pas de profil courant sélectionné OU user pas connecté

## Supprimer un commentaire

Supprime un commentaire d'un post

### URL
```
/comment/delete/<commentID>
```

### Méthode

**GET**

### Variables GET

* **commentID** : ID du commentaire à supprimer

### Succès

  * **Code:** 200
Data:
```json
{
    commentID : ID du commentaire supprimé
}
```

### Erreurs

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id du commentaire ne renvoie à aucun commentaire

  OU

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** Pas de profil courant sélectionné OU User pas connecté OU vous ne pouvez pas supprimé un commentaire qui n'est pas le vôtre.

## Liker un commentaire

Ajoute un like sur un commentaire

### URL
```
/comment/like/<commentID>
```

### Méthode

**GET**

### Variables GET

* **commentID** : ID du commentaire à like

### Succès

  * **Code:** 200
Data:
```json
{
    commentID : ID du commentaire,
    profileID : profil qui vient de mettre un like
}
```

### Erreurs

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id du commentaire ne renvoie à aucun commentaire

  OU

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** Pas de profil courant sélectionné OU User pas connecté OU vous ne suivez pas la personne

  OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Vous avez déjà aimé ce commentaire OU vous ne pouvez pas aimer votre propre commentaire

## Dé-liker un commentaire

Supprime un like sur un commentaire

### URL
```
/comment/unlike/<commentID>
```

### Méthode

**GET**

### Variables GET

* **commentID** : ID du commentaire à unlike

### Succès

  * **Code:** 200
Data:
```json
{
    commentID : ID du commentaire,
    profileID : profil qui vient de dé-like
}
```

### Erreurs

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id du commentaire ne renvoie à aucun commentaire

  OU

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** User pas connecté

  OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Vous ne pouvez pas dé-like un post que vous n'avez pas encore like

## Récupérer tous les likes d'un commentaire

Récpuère tous les likes et informations d'un commentaires

### URL
```
/comment/likes/<commentID>
```

### Méthode

**GET**

### Variables GET

* **commentID** : ID du commentaire à supprimer

### Succès

  * **Code:** 200
Data:
```json
{
    commentID : ID du commentaire,
    nbOfLikes : nombre de likes,
    likes : [{
        comment_id : ID du commentaires,
        profile_id : ID du profil qui a like,
        profile_name : Nom du profil qui a aimé,
        like_time : Date du like
    }]
}
```

### Erreurs

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id du commentaire ne renvoie à aucun commentaire

  OU

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** Vous ne suivez pas la personne


