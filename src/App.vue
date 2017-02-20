<template>
  <div id="app">
    <header id="common-header">
      <search class="header-item"></search>
      <a id="logo" class="header-item" title="Accueil" href="/"><img alt="Eiko" src="./assets/Eiko.png"/></a>
      <connexion id="connexion" class="header-item"></connexion>
    </header>
    <router-view></router-view>
  </div>
</template>

<script>
let search = require('./components/Search.vue');
let connexion = require('./components/Connexion.vue');

export default {
  name: 'app',
  components : {
    search,
    connexion
  }
};
</script>

<style lang="css" scoped>
  #logo {
    text-align: center;
  }
  #connexion {
    text-align: right;
  }
  header{
    width: 100%;
    margin : 0;
    position: relative;
    top: 0;
    left: 0;
    right: 0;

    background-color: lightgray;
    display : flex;
  }
  .header-item {
    flex : 1;
    display : inline-block;
    margin : 20px;
    vertical-align: middle;
  }
</style>
