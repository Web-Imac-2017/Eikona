<template lang="html">
  <div>
    <div v-if="!connected">
      <button @click="connexion">Connexion</button>
      <button @click="inscription">Inscription</button>
    </div>
    <div v-else>
      <button @click="settings"> {{ user.id }}</button>
      <button @click="deconnect">Se déconnecter</button>
    </div>
  </div>
</template>

<script>
import store from './connectionStore.js'

export default {
  name: 'header-connection',
  store: store,
  data () {
    return {
      user: {
        id: 'none'
      },
      connected: false
    }
  },
  methods: {
    connexion () {
      console.log('connexion')
      store.commit('SET_CURRENT_URL', window.location.pathname)
      document.location.href = '/#connectionForm'
    },
    inscription () {
      console.log('inscription')
      store.commit('SET_CURRENT_URL', window.location.pathname)
      document.location.href = '/#inscriptionForm'
    },
    deconnect () {
      console.log('deconnect')
    },
    settings () {
      console.log('settings')
      window.location = '/user/' + this.user.id + '/settings'
    }
  }
}
</script>

<style scoped>
  form > * {
    display : inline-block;
  }
</style>
