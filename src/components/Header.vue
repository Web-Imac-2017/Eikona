<template lang="html">
  <md-layout tag="header" id="header">
    <search class="header-item"></search>
    <md-layout class="header-item">
      <md-button id="logo" title="Accueil" href="/Eikona/" class="md-icon-button"><md-icon alt="Eiko" md-src="assets/Eiko.png"/></md-button>
    </md-layout>
    <connexion id="connexion" class="header-item"></connexion>
  </md-layout>
</template>

<script>
let search = require('./Search.vue')
let connexion = require('./Header-connection.vue')

export default {
  name: 'top-header',
  components: {
    search,
    connexion
  }
}
</script>

<style lang="css" scoped>
#header {
  width:100%;
  background-color: lightgray;
}
.header-item {
  margin: auto 0;
}
</style>
