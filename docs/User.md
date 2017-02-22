# Utilisateurs

Les utilisateurs sont gérés pas le **UserController**. Il permet d'accèder à toutes les informations d'un utilisateur.

## Edition des informations de l'utilisateur

### URL

```
/user/edit/<field>/<userID>
```

### Methode
**POST**

### Variables GET

* **field** : Nom du champ à modifier `NAME|EMAIL|PASSWORD`
* **userID** : ID de 'l'utilisateur

### Erreurs

* **Code:** 401 NOT AUTHORIZED <br />
  **Explication** L'utilisateur n'est pas autorisé à mettre à jour les informations.

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

## Field = password

### Variables POST

* **passwd** : Le nouveau mot de passe de l'utilisateur
* **passwd_confirm** : La confirmation du nouveau mot de passe

### Succès

* **Code:** 200 OK <br />

### Erreurs

* **Code:** 400 BAD REQUEST <br />
  **Explication** Au moins une des variables POST n'a pas été transmise.

  OU

* **Code:** 409 CONFLICT <br />
  **Explication** Le nouveau mot de passe est incorrect OU les mots de passe ne sont pas identiques.
