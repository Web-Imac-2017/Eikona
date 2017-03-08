# Authentification

Seront détaillés ici les méthodes pour l'authentification d'un user, à savoir, son incsription, l'activation de son compte et sa connexion.

## Inscription d'un utilisateur

Créer un compte pour le nouvel utilisateur 

### URL
```
/auth/register/
```

### Méthode
**POST**

### Variables POST

* **user_name** : Nom de l'utilisateur
* **user_email** : Email de l'utilisateur (valueur unique)
* **user_passwd** : Mot de passe de l'utilisateur 
* **user_passwd_confirm** : Confirmation du mot de passe

### Succès

  * **Code:** 201 CREATED 
Data:
```json
{
  email: Email du user ajouté,
  userID: id du user ajouté
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 400 BAD REQUEST <br />
  **Explication** Le mail n'a pas été envoyé.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le mot de passe et celui de confirmation ne correpsondent pas.

  OU

* **Code:** 403 FORBIDDEN <br />
  **Explication** L'utilisateur exite déjà. Son mail est déjà présent dans la base de données.

### Notes

Un utilisateur ne peut s'inscrire que si son adresse email est unique. Un email d'activation lui est envoyé à la suite de son enregistrement. 

## Activation du compte d'un utilisateur

Active le compte d'un utilisateur enregistré dans la base de données.

### URL
```
/auth/activate/
```

### Méthode
**POST**

### Variables POST

* **user_id** : ID de l'utilisateur
* **user_key**: Clé cryptée pour sécuriser l'activation (correspond au sha1 du regiter_time)


### Succès

  * **Code:** 200 OK
Data:
```json
{
	userID: ID du user
}
```

### Erreurs

* **Code:** 409 CONFLICT <br />
  **Explication** user_id et/ou user_key n'existe(nt) pas.

  OU

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

### Notes

Une fois son compte activé, il peut se connecter su site et accéder aux fonctionnalités réservées aux membres connectés.

## Connexion

Permet à l'utilisateur de se connecter à son compte et d'accéder à diverses fonctionnalités.

### URL
```
/auth/signIn/
```

### Méthode
**POST**

### Variables POST

* **user_email** : Email de l'utilisateur
* **user_passwd**: Mot de passe de l'utilisateur


### Succès

  * **Code:** 200 OK
Data: 
```json
{
  userID: ID du user,
  userEmail: Email du user
}
```
```html
$_SESSION['userID']
```
Enregistrement dans une variable de session l'id de l'utilisateur.

### Erreurs

* **Code:** 401 UNAUTHORIZED <br />
  **Explication** Le compte de l'utilisateur n'est pas activé.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le mot de passe ne correspond pas **MAIS** l'adresse mail est correcte.

  OU

* **Code:** 404 NOT FOUND <br />
  **Explication** L'utilisateur (adresse email) est inconnu(e).

  OU

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

## Déconnexion

Permet à l'utilisateur de se déconnecter de son compte.

### URL
```
/auth/signOut/
```

### Méthode

**GET**

### Succès

  * **Code:** 200 OK
Data: 
```json
{
  userID: ID du user,
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** L'utilisateur n'est connecté à aucun compte.

## Récupération du mot de passe

La récupération du mot de passe se passe en deux étapes. Dans un premier temps, l'utilisateur "dira" qu'il a oublié son mot de passe. Il recevra par mail un code qui lui permettra de redéfinir un nouveau mot de passe.
Ici est détaillée la première étape.

### URL
```
/auth/forgottenPassword/
```

### Méthode

**POST**

### Variables POST

* **user_email** : Email de l'utilisateur

### Succès

  * **Code:** 200 OK
Data: 
```json
{
  userEmail: Email du user,
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Tous les champs ne sont pas remplis 

  OU 

* **Code:** 404 NOT FOUND <br />
  **Explication** L'email ne correspond à aucun utilisateur

## Regénération du mot de passe

Une fois le code reçu, il pourra redéfinir un nouveau mot de passe.

### URL
```
/auth/regenere/
```

### Méthode

**POST**

### Variables POST

* **user_email** : Email de l'utilisateur
* **user_passwd** : Nouveau mot de passe
* **user_passwd_confirm** : Confirmation du nouveau mot de passe
* **code** : Le code reçu par mail

### Succès

  * **Code:** 200 OK
Data: 
```json
{
  userEmail: Email du user,
}
```

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Tous les champs ne sont pas remplis 

  OU 

* **Code:** 404 NOT FOUND <br />
  **Explication** L'email ne correspond à aucun utilisateur

    OU 

* **Code:** 409 CONFLICT <br />
  **Explication** Le code pour redéfinir un mot de passe est incorrect

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le mot de passe et celui de confirmation ne correspondent pas



