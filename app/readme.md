# Eikona

> WebImac

## Les routes (Back-API)

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

## Build Setup

``` bash
# install dependencies
npm install

# serve with hot reload at localhost:8080
npm run dev

# build for production with minification
npm run build

# lint all *.js and *.vue files
npm run lint

```

For more information see the [docs for vueify](https://github.com/vuejs/vueify).
