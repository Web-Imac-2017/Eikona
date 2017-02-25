<template lang="html">
  <div>
    <div v-if="!connected">
      <md-button @click="connexion">Connexion</md-button>
      <md-button @click="inscription">Inscription</md-button>
    </div>
    <div v-else>
      <md-button @click="settings"> {{ user.id }}</md-button>
      <md-button @click="deconnect">Se déconnecter</md-button>
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
        id: 'none',
        connected: false
      }
    }
  },
  methods: {
    connexion () {
      console.log('connexion')
      window.location = '/#connectionForm'
      document.getElementById('connection-id').focus()
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

</style>
