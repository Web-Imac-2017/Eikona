<template lang="html">
  <md-layout>
    <div v-if="user.connected">
      <md-button @click.native="settings" class="md-icon-button"><md-icon>settings</md-icon></md-button>
      <md-button @click.native="deconnect" class="md-icon-button"><md-icon>power_settings_new</md-icon></md-button>
    </div>
    <div v-else>
      <md-button @click.native="connexion">Connexion</md-button>
      <md-button @click.native="inscription">Inscription</md-button>
    </div>
  </md-layout>
</template>

<script>
import store from './connectionStore.js'
import Vuex from 'vuex'

export default {
  name: 'header-connection',
  store: store,
  computed: {
    user () {
      return this.$store.state.user
    }
  },
  methods: {
    connexion () {
      console.log('connexion')
      this.$router.push('/#connectionForm', () => { document.getElementById('connection-id').focus() })
    },
    inscription () {
      console.log('inscription')
      this.$router.push('/#inscriptionForm')
    },
    deconnect () {
      console.log('deconnect')
      this.$http.post('/Eikona/do/auth/signOut/', {}).then((response) => {
        console.log('Disconnected', response)
        store.commit('SET_USER', 0, 0, false)
      }, (response) => {
        console.log('ERR: disconnect', response)
        switch(response.code){
          case 400:
            console.log('Bad request')
            this.error_message = 'Erreur de connexion. Veuillez ressayer plus tard.'
            break
          case 401:
            console.log('Unauthorized')
            this.error_message = 'Votre compte n\'est pas activé.'
            break
          case 404:
            console.log('Not found')
            this.error_mail = true
            document.getElementById('connection-id').className += " md-input-invalid"
            break
          case 409:
            console.log('Conflict')
            this.error_password = true
            break
          default:
            console.log('Unknown error')
        }
      })
    },
    settings () {
      console.log('settings')
      this.$router.push('/user/settings')
    }
  }
}
</script>

<style scoped>

</style>
