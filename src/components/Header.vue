<template lang="html">
  <header>
    <search class="header-item"></search>
    <a id="logo" class="header-item" title="Accueil" href="/"><img alt="Eiko" src="./assets/Eiko.png"/></a>
    <connexion id="connexion" class="header-item"></connexion>
  </header>
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
  margin : 10px;
  vertical-align: middle;
}
.header-item img {
  max-height : 20px;
}
</style>
