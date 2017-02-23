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
  data () {
    return {
      user: {
        id: 'none',
        connected: false
      }
    }
  },
  methods: {
    connexion () {
      console.log('connexion')
      window.location = '/#connectionForm'
    },
    inscription () {
      console.log('inscription')
      window.location = '/#inscriptionForm'
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
