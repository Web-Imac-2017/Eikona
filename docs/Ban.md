
# Les mots et emails bannis

La gestions méchants mots et emails *persona non grata* se fait par l'intermédiaires du **BanController**.





## Ajout d'un ban

Ajoute un mot et un email à la liste des entités bannies 

### URL
```
/ban/add/<word|email>[/<element>]
```

### Méthode
**POST**

### Variables GET

  * **word|email** : Type d'élément à bannir (word ou email)
  
### Variable POST
  
  * **word** ou **email** : Element à bannir selon le type d'élément à bannir

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST est manquante ou une erreur est présente dans les paramètres.

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** Vous ne pouvez pas ajouter d'élément (Vous n'êtes pas administrateur)

  OU

  * **Code:** 409 CONFLICT <br />
    **Explication** Cette élément est déjà bannie
    





## Supression d'un ban

Retire un mot et un email à la liste des entités bannies 

### URL
```
/ban/remove/<word|email>
```

### Méthode
**POST**

### Variables GET

  * **word|email** : Type d'élément à suprimmer (word ou email)
  
### Variable POST
  
  * **word** ou **email** : Element à retirer selon le type d'élément à bannir

### Succès

  * **Code:** 200 OK <br />
    **Data:** `{ }`
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST est manquante ou une erreur est présente dans les paramètres.

  OU

  * **Code:** 401 NOT AUTHOROZED <br />
    **Explication** Vous ne pouvez pas ajouter d'élément (Vous n'êtes pas administrateur)





## Vérification d'un ban

Vérifie la présence d'un mot ou d'un email dans la liste des entités bannies 

### URL
```
/ban/is/<word|email>[/<element>]
```

### Méthode
**POST** OU **GET**

### Variables GET

  * **word|email** : Type d'élément à vérifier (word ou email)
  
  Si Méthode GET
  
  * **element** : Element à vérifier
  
### Variable POST

  Si Méthode POST
  
  * **word** ou **email** : Élement à vérifier selon le type d'élément à vérifier

### Succès

  * **Code:** 200 OK <br />
    **Data:** 
    ```
    {
        exists : 1 ou 0 selon si l'élément est banni ou non.
    }
    ```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST OU GET est manquante ou une erreur est présente dans les paramètres.





## Récupération de la liste

Récupère la liste de tous les éléments d'un certain type.

### URL
```
/ban/get/<word|email>
```

### Méthode
**GET**

### Variables GET

  * **word|email** : Type d'élément à vérifier (word ou email)

### Succès

  * **Code:** 200 OK <br />
    **Data:** 
    ```
    {
        nbrWords|nbrEmails : Nombre d'éléments retournés
        words|emails : tableau contenant tous les éléments retournés.
    }
    ```
 
### Erreurs

  * **Code:** 400 BAD REQUEST <br />
    **Explication** La variable POST est manquante ou une erreur est présente dans les paramètres.