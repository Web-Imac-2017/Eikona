# Recherches

La recherches est activée pour tous les utilisateurs. La fonction de recherche permet de retourner des résultats en fonction d'une 'query' qui est un mot ou un bout de phrase. 
On peut rechercher : 

* Des profils en fonction du nom
* Des posts en fonction de la description
* Des commentaires en fonction de leur contenu
* Des posts en fonction des tags dans la description 

La recherche est gérée par le SearchController.

## Recherche sur le site

Recherche des correspondances avec une chaîne de caractères.
Par défaut, le recherche s'effectue sur tous les champs cités ci-dessus.

### URL

```
/search/
```

### Méthode

**POST**

### Variables POST

* **query** : Valeur de la recherche

### Variables POST optionnelles

* **field** : Permet de cibler la recherche sur une table 

**Valeurs de field**

* **profile** : Permet de rechercher des profiles
* **description** : Permet de rechercher des posts en fonction des descriptions 
* **comment** : Permet de rechercher un post en fonction des commentaires
* **tag** : Permet de chercher un post en fonction des tags

### Succès 

* **Code** 200 OK
Data : 
```json
{
    
}
```
