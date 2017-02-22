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

* **Code:** 201 CREATED <br />
  **Data:** 
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

* **Code:** 409 CONFLICT <br />
  **Explication** Le mot de passe et celui de confirmation ne correpsondent pas.

  OU

* **Code:** 403 FORBIDDEN <br />
  **Explication** L'utilisateur exite déjà. Son mail est déjà présent dans la base de données.

### Notes

Un utilisateur ne peut s'inscrire que si son adresse email est unique.

