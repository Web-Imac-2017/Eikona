# Les routes

Afin de simplifer les appels aux controleurs, un système de route est mis en place.
Il fait appel à un format d'URL spécifique permettant de spécifier clairement quel contrôleur doit être appelé, pour quel action, et avec quels paramètres.

Toutes les routes prennent le format suivant :
```
domain.com/do/<controller>/<action>/<param1>/.../<paramN>
```
Par exemple, une route demandant à mettre à jour la description d'un profil pourrait avoir cette forme :
```
domain.com/do/profils/update/description/
```
