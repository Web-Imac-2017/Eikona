# Profils

Les profils sont gérés par le **profilesController**.
Celui-ci permet d'accèder à toutes les informations relatives à ces derniers.

Création d'un profil
----
  Crééer un profil pour l'utilisateur courant.

* **URL**

    `/profiles/create/`

* **Méthode:**
  
  `POST`


* **Variables POST**

  * **profileName** : Nom du profil
  
  **Optionnel**
  
  * **profileDesc** : Description du profil
  * **profilePrivate** : À transmettre si le profil est privé

* **Succès:**

  * **Code:** 201 CREATED <br />
    **Data:** `{ profileID : ID du profil créé }`
 
* **Erreurs:**

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST **profileName** n'a pas été transmise.

  OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Le nom de profil est déjà utilisé.

* **Notes:**

  Un profil ne peut être créer si il n'y a pas d'utilisateur de connecté.