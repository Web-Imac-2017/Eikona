<template>
  <div id="app">
    <header id="common-header">
      <search></search>
      <a title="Accueil" href="/"><img alt="Eiko" src=""/></a>
      <connexion></connexion>
    </header>
    <router-view></router-view>
  </div>
</template>

<script>
let search = require('./components/Search.vue');
let connexion = require('./components/Connexion.vue');
export default {
  name: 'app'
};
</script>

<style lang="css">
  #common-header{
    width: 100%;
    position:
  }
</style>
