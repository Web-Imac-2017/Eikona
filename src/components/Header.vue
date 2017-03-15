<template lang="html">
  <md-layout tag="header" id="header">
    <search class="header-item"></search>
    <md-layout class="header-item" md-align="center">
      <md-button id="logo" title="Accueil" class="md-icon-button" @click.native="goHome"><md-icon alt="Eiko" md-src="assets/Eiko.png"/></md-button>
    </md-layout>
    <connexion id="connexion" class="header-item"></connexion>
  </md-layout>
</template>

<script>
import search from './Search.vue'
import connexion from './Header-connection.vue'
import store from './connectionStore.js'
import Vuex from 'vuex'

export default {
  name: 'top-header',
  components: {
    search,
    connexion
  },
  computed: {
    ...Vuex.mapGetters([
      'getUser'
    ])
  },
  methods: {
    goHome () {
      if (this.getUser.userName) this.$router.push('/user')
      else this.$router.push('/')
    }
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
