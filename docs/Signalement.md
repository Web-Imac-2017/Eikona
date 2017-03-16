
# Les Signalements

Les signalements sont gérés par le **reportController**.
Celui-ci permet d'ajouter un signalement, de gérer un signalement pour un modérateur, de le signaler s'il est légitime ou de l'annuler. Permet aussi de récupérer tous les signalements (non pris par un modo) ou tous ses propres signalements.



## Ajout d'un signalement

Ajoute un signalement pour le post donné avec un commentaire.

### URL
```
/report/add/{{postID}}
```

### Méthode
**GET**&**POST**

### Variables POST

  * **postID** : ID du post

### Variables POST

  **Variables optionnelles**

  * **reportComment** : Commentaire pour le signalement

### Succès

  * **Code:** 201 REPORT ADD <br />
    **Data:** 
    ```
    { 
    	reportID : ID du report crée
    }
    ```
    
### Erreurs

  * **Code:** 400 NOT ADDED <br />


## Affichage des informations d'un signalement

Affichage des infos d'un signalement

### URL
```
/post/display/<reportID>
```

### Méthode
**GET**

### Variables POST

  * **postID** : ID du post

### Succès

  * **Code:** 200 OK <br />
Data: 
```json
{
  reportID : ID du report,
  userID : ID du profil,
  postID : ID du post,
  reportComment : commentaire de la personne qui avait signalé
  reportStatus : statut du report, 0 - pas pris par un modo, 1 - pris mais pas géré, 2 - post caché attente d'une modification ou 3 - fini/annulé.
  reportHandler : ID du modérateur qui gère le signalement
  reportResult : Résultat final par le modérateur
  timeStateChange : Moment où le signalement passe de 1 à 2
}
```

### Erreurs

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le signalement n'existe pas.


## Se charger d'un signalement pour un modérateur

Permet à un modérateur de se charger d'un signalement, le reportStatus passe à 1.

### URL
```
/post/handle/<reportID>
```

### Méthode

**GET**

### Variable GET

    **postID** : ID du post à utiliser


### Succès

  * **Code:** 200 OK
    **Explication** Report already reported ou already finished ou has already an handler.
	
Data:
```json
{ 
    ReportHandlerID : ID du modérateur, 
    Status: Status du report passe à 1,
	ReportID: ID du signalement
}
```

### Erreurs

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le signalement n'existe pas.


  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** L'user actuel n'est pas un modérateur. 
	

## Annuler un signalement qui n'est pas légitime

Annuler un signalement qui n'est pas légitime


### URL
```
/post/cancel/<reportID>
```

### Méthode

**GET**

### Variable GET

    **postID** : ID du report

### Variable GET

    **report_result** : Message à envoyer par le modérateur 

### Succès

  * **Code:** 200 OK
    **Explication** Le signalement est fini ou est déjà fini. 
	
Data:
```json
{ 
    ReportHandlerID : ID du modérateur, 
    Status: Status du report passe à 1,
	ReportID: ID du signalement
}
```

### Erreurs

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le signalement n'existe pas.

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** L'user actuel n'est pas un modérateur. 
	
 
## "Signaler" un signalement qui était pas légitime

Modérateur signale un signalement qui est pas légitime, statut change à 2. Post devient caché. 


### URL
```
/post/reported/<reportID>
```

### Méthode

**GET**

### Variable GET

    **postID** : ID du report

### Variable GET

    **report_result** : Message à envoyer par le modérateur 

### Succès

  * **Code:** 200 OK
    **Explication** Le signalement est envoyé et la notif est envoyé
	
Data:
```json
{ 
	ReportID: ID du signalement,
	PostID: ID du post signalé,
	PostState: Status du report passe à 2
}
```

### Erreurs

  * **Code:** 404 NOT FOUND <br />
    **Explication** Le signalement n'existe pas.

  OU

  * **Code:** 401 NOT AUTHORIZED <br />
    **Explication** L'user actuel n'est pas un modérateur. 

  OU

  * **Code:** 201 ALREADY REPORTED/FINISHED<br />
    **Explication** Le signalement est déjà fini/signalé. 
	
 
 ## Recherche tous les signalement qu'un modérateur gère. Uniquement posts qui ont une date de modification supérieur à la date de signalement

Pour récupérer seulement les posts cachés qui ont été modifiés depuis qu'ils ont étaient cachés


### URL
```
/post/waiting/
```

### Méthode

**GET**

### Variable GET

    **report_result** : Message à envoyer par le modérateur 

### Succès

  * **Code:** 200 OK
    **Explication** Listes des signalements qui correspondent. Affiche pour chaque report ses informations.
	
Data: 
```json
{
  reportID : ID du report,
  userID : ID du profil,
  postID : ID du post,
  reportComment : commentaire de la personne qui avait signalé
  reportStatus : statut du report, 0 - pas pris par un modo, 1 - pris mais pas géré, 2 - post caché attente d'une modification ou 3 - fini/annulé.
  reportHandler : ID du modérateur qui gère le signalement
  reportResult : Résultat final par le modérateur
  timeStateChange : Moment où le signalement passe de 1 à 2
}
```

### Erreurs

  * **Code:** 400 NOT FOUND <br />
    **Explication** Pas de signalements qui correspondent.


 ## Recherche tous les signalement non handle par un modérateur OU tous les signalements du modérateur



### URL
```
/post/reports/
```

### Méthode

**GET**

### Variable POST

    **my_reports** : Si cette variable existe on veut les reports du modérateur, sinon on veut tous les reports dont le statut est à 0. 

### Succès

  * **Code:** 200 OK
    **Explication** Listes des signalements qui correspondent. Affiche pour chaque report ses informations.
	
Data: 
```json
{
	nbOfReports: nombres de reports retournés
	Reports: les reports

}
```