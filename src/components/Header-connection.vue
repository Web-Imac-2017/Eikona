<template lang="html">
  <md-layout md-align="end">
    <div v-if="getUser.userName != false">
      <md-button @click.native="settings" class="md-icon-button"><md-icon>settings</md-icon></md-button>
      <md-button @click.native="disconnect" class="md-icon-button"><md-icon>power_settings_new</md-icon></md-button>
    </div>
    <div v-else>
      <md-button @click.native="connexion">Connexion</md-button>
      <md-button @click.native="inscription">Inscription</md-button>
    </div>
    <md-snackbar md-position="top right" ref="snackbar" md-duration="3000">
      <span>Vous n'êtes connectés à aucun compte.</span>
      <md-button class="md-accent" md-theme="light-blue" @click.native="$refs.snackbar.close()">Réessayer</md-button>
    </md-snackbar>
  </md-layout>
</template>

<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import apiRoot from './../config.js'

export default {
  name: 'header-connection',
  store: store,
  computed: {
    ...Vuex.mapGetters([
      'getUser'
    ])
  },
  methods: {
    ...Vuex.mapActions([
      'clearUser',
      'clearProfiles'
    ]),
    connexion () {
      console.log('connexion')
      this.$router.push('/#connectionForm', () => { document.getElementById('connection-id').focus() })
    },
    inscription () {
      console.log('inscription')
      this.$router.push('/#inscriptionForm')
    },
    disconnect () {
      this.$http.post(apiRoot + 'auth/signOut/', {}).then((response) => {
        this.clearUser()
        this.clearProfiles()
        this.$router.push('/')
      }, (response) => {
        switch (response.status) {
          case 400:
            console.log('Bad request')
            this.$refs.snackbar.open();
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
