<template lang="html">
  <header>
    <search class="header-item"></search>
    <div class="header-item">
      <a id="logo" title="Accueil" href="/"><md-icon alt="Eiko" class="md-size-2x" md-src="./assets/Eiko.png"/></a>
    </div>
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
header {
  width:100%;
  background-color: lightgray;
  display: flex;
}
.header-item {
  flex: 1;
  text-align: center;
  margin: auto 0;
}

</style>
