# Utilisateurs

Les utilisateurs sont gérés pas le **UserController**. Il permet d'accèder à toutes les informations d'un utilisateur.

## Edition des informations de l'utilisateur

### URL

```
/user/edit/<field>/
```

### Methode
**POST**

### Variables GET

* **field** : Nom du champ à modifier `NAME|EMAIL|PASSWORD`

### Erreurs

* **Code:** 401 NOT AUTHORIZED <br />
  **Explication** L'utilisateur n'est pas autorisé à mettre à jour les informations.

  OU

* **Code:** 405 METHOD NOT ALLOWED <br />
  **Explication** La méthode n'existe pas. Le "field" est incorrect.

## Field = name

### Variables POST

* **name** : Le nouveau nom de l'utilisateur

### Succès

  * **Code:** 200 OK
Data:
```json
{
	userID: ID du user
	userName: nouveau nom du user
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le nouveau nom est incorrect. 

## Field = email

### Variables POST

* **email** : Le nouvel email de l'utilisateur

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userID: ID du user
  userEmail: nouveau email du user
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le nouvel email est incorrect. 

  OU

* **Code:** 403 FORBIDDEN <br />
  **Explication** L'adresse mail est déjà présente dans la base de données. Elle n'est pas changée.

## Field = password

### Variables POST

* **passwd** : Le nouveau mot de passe de l'utilisateur
* **passwd_confirm** : La confirmation du nouveau mot de passe

### Succès

  * **Code:** 200 OK

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le nouveau mot de passe est incorrect OU les mots de passe ne sont pas identiques.

## Edition des droits d'accès de l'utilisateur

### URL

```
/user/rules/<field>/
```

### Methode

**POST**

### Variables GET

* **field** : Nom du champ à modifier `SETMODERATOR|SETADMIN|SETUSER`

### Erreurs

* **Code:** 401 NOT AUTHORIZED <br />
  **Explication** L'utilisateur n'est pas autorisé à mettre à jour les informations. Seuls les administrateurs peuvent changer les droits des autres utilisateurs.

* **Code:** 405 METHOD NOT ALLOWED <br />
  **Explication** La méthode n'existe pas. Le "field" est incorrect.

## Field = setModerator

### Variables POST

* **id** : L'id de l'utilisateur dont on va changer les droits.

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userModeratorID: ID du user qui est maintenant modérateur,
  userModerator: 1 (true). Pour dire que le user est modérateur
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** L'id est incorrect. 

  OU

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id ne renvoie à auncun utilisateur.

## Field = setAdmin

### Variables POST

* **id** : L'id de l'utilisateur dont on va changer les droits.

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userAdminID: ID du user qui est maintenant modérateur,
  userModerator: 1 (true). Pour dire que le user est modérateur,
  userAdmin: 1 (true). Pour dire que le user est admin
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** L'id est incorrect. 

  OU

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id ne renvoie à auncun utilisateur.

  ## Field = setModerator

### Variables POST

* **id** : L'id de l'utilisateur dont on va changer les droits.

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userModeratorID: ID du user qui est maintenant modérateur,
  userModerator: 1 (true). Pour dire que le user est modérateur
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** L'id est incorrect. 

  OU

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id ne renvoie à auncun utilisateur.

## Field = setUser

### Variables POST

* **id** : L'id de l'utilisateur dont on va changer les droits. Il va redevenir un simple utilisateur. 

### Succès

  * **Code:** 200 OK
Data:
```json
{
  oldModeratorAdminID: ID du user qui est maintenant user simple,
  userModerator: 0 (false). Pour dire que le user est plus modérateur,
  userAdmin: 0 (false). Pour dire que le user est plus admin
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** L'id est incorrect. 

  OU

* **Code:** 404 NOT FOUND <br />
  **Explication** L'id ne renvoie à auncun utilisateur.

## Récupération de toutes les informations de l'utilisateur courant

### URL

```
/user/get/
```

### Methode

**POST**

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userID: ID du user dont les infos sont récupérées,
  userName: nom du user,
  userEmail: email du user,
  userRegisterTime: date de son inscription,
  userLastActivity: date de sa dernière activité sur le site,
  userModerator: si il est moderateur ou non,
  userAdmin: si il est admin ou non,
  userActivated: si son compte est activé ou non
}
```

### Notes : 
Le mot de passe n'est pas retourné pour la sécurité. Cette fonction récupère les informations de l'utilisateur courant. 

## Récupération de tous les profils de l'utilisateur courant

### URL

```
/user/profiles/
```

### Methode

**POST**

### Succès

  * **Code:** 200 OK
Data:
```json
{
  userID: ID du user,
  nbOfProfiles: nombre de profils que possède le user,
  profile1:{
    profile_id: id du profil,
    user_id: id du user (== userID),
    profile_name: nom du profil,
    profile_desc: description du profil,
    profile_create_time: date de création du profil,
    profile_views: nombre de vues du profil,
    profile_private: si le profil est privé ou non
  },
  profile2:{
    même champs que profile1
  },
  ...
}
```
