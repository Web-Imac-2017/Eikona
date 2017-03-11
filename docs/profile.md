
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






## Nombre de posts d'un profil

Récupère le nombre de posts fait par un profil

### URL
```
/profile/nbrposts/<profileID>
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
    nbrPosts : Nombre de posts
}
```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Le profil courant n'est aps autorisé a voir les posts de ce profil

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
    posts : Tableau avec les détails de touts les posts trouvés, sur le même format que `do/posts/display`
}
```
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Le profil courant n'est aps autorisé a voir les posts de ce profil

  OU

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le profil spécifié n'existe pas







## Brouillons du profil

Récupère les brouillons du profil spécifié

### URL
```
/profile/drafts/<profileID>
```

**Note** Les crochets indiquent une valeur optionnelle. L'ordre des paramètres entre croches n'a pas d'importance.

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil à utiliser

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
    nbrPosts : Nombre de brouillons trouvés, 
    posts : Tableau avec les détails de touts les brouillons trouvés, sur le même format que `do/posts/display`
}
```
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID

  OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Le profil courant n'est aps autorisé à accèder aux brouillons du profil

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







## Nombre d'abonnés à profil

Retourne le nombre de profils abonnés au profil donné

### URL
```
/profile/nbrFollowers/<profileID>/
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID: ID du profil,
    nbrFollowers: Nombre de followers
}
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente







## Nombre d'abonnements d'un profil

Retourne le nombre d'abonnement du profil donné

### URL
```
/profile/nbrFollowings/<profileID>/
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID: ID du profil,
    nbrFollowings: Nombre de followers
}
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID OU la variable FILE profilePicture est absente







## Abonnement à un profil

Abonne le profil courant au profil spécifié

### URL
```
/profile/follow/<profileID>[/<subscribe>]
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil
  
  **Valeur optionnelle**
  
  * **subscribe** 1 ou 0 si l'on souhaite recevoir des notifications

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID
    
    OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Il n'y a pas de profile connecté OU Vous n'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même
    
    OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Vous suivez déjà ce profil







## Désabonnement à un profil

Désabonne le profil courant du profil spécifié

### URL
```
/profile/follow/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID
    
    OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Il n'y a pas de profile connecté OU Vous n'avez pas les droits sur ce profil OU Vous ne pouvez pas arrêter de vous suivre vous-même.
    
    OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Vous ne suivez déjà pas ce profil







## Liste des abonnés à un profil

Retourne la liste des abonnés à un profil

### URL
```
/profile/followers/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID: ID du profil,
    nbrFollowers: Nombre de followers
    followers: Tableau avec les détails de tous les profils retournés
}
```

### Erreurs

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Vous n'avez pas le droit de voir cette liste







## Liste des abonnements d'un profil

Retourne la liste des abonnement d'un profil

### URL
```
/profile/followings/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{
    profileID: ID du profil,
    nbrFollowings: Nombre d'abonnements'
    followings: Tableau avec les détails de tous les profils retournés
}
```

### Erreurs

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Vous n'avez pas le droit de voir cette liste







## Recevoir les notifications pour un abonnement

Active les notifications pour le profil courant pour l'abonnement

### URL
```
/profile/subscribe/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à modifier ce profil OU Vous n'êtes pas connecté
    
    OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Vous ne pouvez pas recevoir les notification si vous n'êtes pas abonné







## Ne plus recevoir les notifications pour un abonnement

Désactive les notifications pour le profil courant pour l'abonnement

### URL
```
/profile/subscribe/<profileID>
```

### Méthode
**GET**

### Variable GET

  * **profileID** : ID du profil

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Vous n'êtes pas autorisé à modifier ce profil OU Vous n'êtes pas connecté
    
    OU

  * **Code:** 400 BAD REQUEST <br />
    **Explication** Vous ne pouvez pas ne plus recevoir les notification si vous n'êtes pas abonné







## Vérifie l'abonnement

Vérifie si un profil est abonné à un autre

### URL
```
/profile/isFollowing/<followedID>[/<followerID>]
```

### Méthode
**GET**

### Variable GET

  * **followedID** : ID du profil suivie
  
  **Variable Optionnelle**
  
  * **followerID** : Profile suivant. le profile courant est utilisé par défaut.

### Succès

  * **Code:** 200 OK
Data:
```json
{
    isFollowing : 1 si le followed est suivant par le profil follower, 0 sinon.
    isSubscribed : 1 si le follower est abonné au profil followed, 0 sinon.
    isConfirmed : 1 si l'abonnement est confirmé, 0 sinon
}
```
    
    







## Confirmer un abonnement

Confirme une demande d'abonnement lorsque le profil est privée

### URL
```
/profile/confirmFollow/<followerID>
```

### Méthode
**GET**

### Variable GET

  * **followerID** : Profil ayant fait la demande de following

### Succès

  * **Code:** 200 OK
Data:
```json
{ }
```

### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable GET **profileID** n'est pas un ID
    
    OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Il n'y a pas de profile connecté OU Vous n'avez pas les droits sur ce profil OU Vous ne pouvez pas arrêter de vous suivre vous-même.
    
    
    
    







## Fil d'actualité du profil (feed)

Le feed contient les derniers évènements des profils suivis pas le profil courant.
Celui-ci inclut : Les posts publiés, les commentaires publiés, les likes, et les nouveaux abonnements.

Les évènements sont triés dans trois catégories : *post*, *comment*, *like*, et *follow*

### URL
```
/profile/feed[/<limit>[/<before>]
```

### Méthode
**GET**

### Variables GET optionnelles

  * **limit** Nombre d'évènement à retourné (Default 30). Attention, le nombre final retourné sera plus petit si des likes/follow sont rassemblés.
  * **before** Timestamp a partir duquel récuperer des évènements. Les évènements sont récupérés du plus récent au plus ancien.

### Succès

  * **Code:** 200 OK
Data:
```json
{ 
  nbrEcents : Le nombre d'évènements retournés
  feed : Un tableau contenant les évènements :
  [{
     type: "post" - Un évènement de type post publié
     time: Timestamp de l'évènement
     postData: Toutes les informations sur le post publié (identique à do/post/display)
     profileData: Toutes les informations sur le profil qui a publié (identique à do/profile/get)
  },{
     type: "comment" - Un évènement de type commentaire ajouté
     time: Timestamp de l'évènement
     postData: Toutes les informations sur le post commenté (identique à do/post/display)
     profileData: Toutes les informations sur le profil qui a commenté (identique à do/profile/get)
     commentData: Toutes les informations sur le commentaire (identique au format utilisé dans do/post/comments)
  },{
    type: "like" -Un évènement de type post(s) aimé(s)
    time: Timestamp du premier élément liké
    profileData: Toutes les informations sur le profil qui a liké (identique à do/profile/get)
    nbrPosts: Nombre de posts présents dans postsData (1 ou +)
    postsData: Array contenant tous les posts likés.(format identique à do/post/display)
  },{
    type: "follow" -Un évènement de type profil(s) suivi(s)
    time: Timestamp du premier post suivi
    profileData: Toutes les informations sur le profil qui a suivi (identique à do/profile/get)
    nbrPosts: Nombre de profils présents dans followedData (1 ou +)
    followedData: Array contenant tous les profils suivis.(format identique à do/profile/get)
  }]
}
```

### Erreurs

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Aucun profil courant n'est sélectionné
    
    OU

  * **Code:** 401 UNAUTHORIZED <br />
    **Explication** Il n'y a pas de profile connecté OU Vous n'avez pas les droits sur ce profil OU Vous ne pouvez pas arrêter de vous suivre vous-même.
