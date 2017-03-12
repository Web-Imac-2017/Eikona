# Notifications

Les notifications sont contrôlées par le NotificationController.

Il y a 6 types de notifications : 

* Un follower aimerait vous suivre (pour les profils privés).
* Vous pouvez désormais suivre ce profil (pour les profil privés).
* Vous avez un nouveau follower.
* Une personne a aimé votre post.
* Une personne a commenté votre post.
* Une personne a aimé votre commentaire

Les notifications sont renvoyés dans un objet notif lui même compris dans le JSON de réponse de la requête. De fait, des controllers ont été modifiés afin de pouvoir renvoyer les notifications en même temps.  

## Format de réponse 

```json
notif : {
    type : type de la notif,
    code : code numérique correspondant,
    profileID : ID du profil à l origine de la notif,
    profileTargetID : ID du profil qui reçoit la notif, 
    targetID : ID de ce qui a été ciblé par la notif
}    
```

## targetID

Le targetID est un id qui renvoie soit sur un post, un commentaire ou un profil, tout dépent de la notification. Voici le tabealu de corresponde entre le type, le code et le targetID

* newFollowAsk   => 1   => profil
* followAccepted => 2   => profil
* newFollowing   => 3   => profil
* newLike        => 4   => post
* newComment     => 5   => post
* newCommentLike => 6   => commentaire

## Récupérer les notifications d'un profil

Récupère toutes les notifs NON VUES du profil courant.

### URL

```
/profile/notifications/
```

### Méthode

**GET**

### Succès 

* **Code** 200 OK
Data : 
```json
    notif:{
        profileID : ID du profil,
        notif : voir format de réponse plus haut
    }
}
```

### Erreurs

* **Code:** 401 UNAUTHORIZED <br />
  **Explciation** pas de profil courant

* **Code:** 404 NOT FOUND<br />
  **Explication** le profil a aucune notifications

## Récupérer les notifications d'un user

Récupère toutes les notifs NON VUES de tous les profils de l'user courant

### URL

```
/user/notifications/
```

### Méthode

**GET**

### Succès 

* **Code** 200 OK
Data : 
```json
    notif:{
        notif : 
            1 
    }
}
```

### Erreurs

* **Code:** 401 UNAUTHORIZED <br />
  **Explciation** pas de profil courant

* **Code:** 404 NOT FOUND<br />
  **Explication** le profil a aucune notifications

