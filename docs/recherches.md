# Recherches

La recherches est activée pour tous les utilisateurs. La fonction de recherche permet de retourner des résultats en fonction d'une 'query' qui est un mot ou un bout de phrase. 
On peut rechercher : 

* Des profils en fonction du nom
* Des posts en fonction de la description
* Des commentaires en fonction de leur contenu
* Des posts en fonction des tags dans la description 

La recherche est gérée par le SearchController.

## Rechercher un profil

Permet de rechercher un profil

### URL

```
/search/profile
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Succès 

* **Code** 200 OK
Data : 
```json

    result: [
      {
        profile_id : id du profil,
        profile_name : nom du profil,
        profile_desc : Description du profil,
        profile_create_time : date de creation du profil,
        profile_views : le nombre de vues,
        profile_private : privé ou public,
        profile_picture : img de couverture
      },
        ........
    ]
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explciation** Il n'y a pas de query

* **Code:** 404 NOT FOUND<br />
  **Explication** Aucun résultat pour la recherche

## Rechercher un post

Permet de rechercher un profil

### URL

```
/search/post
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Succès 

* **Code** 200 OK

```json
{
    result: [
      {
        post_id: id du post,
        profile_id: id du profil qui a posté le post,
        profile_name: nom du profil,
        profile_picture : avatar,
        post_type: type du fichier,
        post_extension: extension du fichier,
        post_description: description du post,
        post_publish_time: date de publication,
        post_edit_time: date de modification,
        post_state: etat du post,
        post_geo_lat: latitude,
        post_geo_lng: longitude,
        post_geo_name: nom du lieu,
        post_allow_comments: commentaires activés ou non,
        post_approved: post approuvé ou non
      },
        ........
    ]
}
```

## Rechercher un commentaire

Permet de rechercher un profil

### URL

```
/search/comment
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Succès 

* **Code** 200 OK

```json
{
    result: [
      {
        comment_id: id du commentaire,
        comment_text: le texte du commentaire,
        comment_time: la date du comm,
        post_id: id du post,
        profile_id: id du profil qui a posté le post,
        profile_name: nom du profil,
        post_type: type du fichier,
        post_extension: extension du fichier,
        post_description: description du post,
        post_publish_time: date de publication,
        post_edit_time: date de modification,
        post_state: etat du post,
        post_geo_lat: latitude,
        post_geo_lng: longitude,
        post_geo_name: nom du lieu,
        post_allow_comments: commentaires activés ou non,
        post_approved: post approuvé ou non
      },
        ........
    ]
}
```

## Rechercher un tag

Permet de rechercher un profil

### URL

```
/search/tag
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Succès 

* **Code** 200 OK

```json
{
    result: [
      {
        tag_name : tag retourné
        post_id: id du post,
        use_time: date utilisation,
        profile_id: id du profil qui a posté le post,
        profile_name: nom du profil,
        post_type: type du fichier,
        post_extension: extension du fichier,
        post_description: description du post,
        post_publish_time: date de publication,
        post_edit_time: date de modification,
        post_state: etat du post,
        post_geo_lat: latitude,
        post_geo_lng: longitude,
        post_geo_name: nom du lieu,
        post_allow_comments: commentaires activés ou non,
        post_approved: post approuvé ou non
      },
        ........
    ]
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explciation** Il n'y a pas de query

* **Code:** 404 NOT FOUND<br />
  **Explication** Aucun résultat pour la recherche

### Recherche sur toute la base

les résultats sont triés pas champ. 
Si un/des profil(s) est/sont trouvé(s), il(s) sera/seront dans un tableau profiles,
posts pour les posts,
comments pour les commentaire,
tags pour les tags

### URL

```
/search/
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Succès 

* **Code** 200 OK

Si aucune valeur est retrouvé, les tableaux vallent NULL

Data:
```json
{
    profiles: null,
    posts: [
        {
            voir recherches par post
        },
        {
            voir recherche par post
        }
    ],
    comments: null,
    tags: [
        {
            voir recherche par tagsa
        }
    ]
}
```
