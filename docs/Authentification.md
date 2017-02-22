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
  **Data:** `{email: Email de l'user ajouté, userID: id de l'user ajouté}` 
