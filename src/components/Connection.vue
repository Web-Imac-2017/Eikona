<template lang="html">
  <md-layout>
    <form id="connectionForm" @submit.stop.prevent="send">
      <h2>Connectez vous</h2>
      <div v-if="error_message != ''" class="md-warn">{{ error_message }}</div>
      <md-input-container id="connection-id">
        <label>E-mail</label>
        <md-input required type="email" v-model="email"></md-input>
        <span class="md-error">Adresse mail inconnue ou incorrecte</span>
      </md-input-container>
      <md-input-container md-has-password id="connection-password">
        <label>Mot de passe</label>
        <md-input required type="password" v-model="password"></md-input>
        <span class="md-error">Mot de passe incorrect</span>
      </md-input-container>
      <p>Les champs marqués d'un * sont obligatoires.</p>
      <md-button class="md-raised" type="submit">SE CONNECTER</md-button>
      <md-button id="forgetPassword" class="md-dense md-accent" @click.native="forgetPassword(true)">Mot de passe oublié ?</md-button>
    </form>
    <md-dialog md-open-from="#forgetPassword" md-close-to="#forgetPassword" ref="frgtPsswd">
      <md-dialog-title>Oubli de mot de passe</md-dialog-title>
      <md-dialog-content>
        <resetPassword @close="forgetPassword(false)"></resetPassword>
      </md-dialog-content>
    </md-dialog>
  </md-layout>
</template>

<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import apiRoot from './../config.js'
import formVerifications from './../formVerifications.js'
import resetPassword from './resetPassword.vue'

export default {
  name: 'connection',
  components: {
    resetPassword
  },
  store: store,
  data () {
    return {
      email: '',
      password: '',
      error_message: ''
    }
  },
  mixins: [formVerifications],
  computed: {
    ...Vuex.mapGetters([
      'profiles'
    ])
  },
  methods: {
    ...Vuex.mapActions({
      initUserStore: 'initUser',
      initProfilesStore: 'initProfiles',
      clearUserStore: 'clearUser'
    }),
    forgetPassword (bool) {
      if(bool){
        this.$refs['frgtPsswd'].open()
        return
      }
      this.$refs['frgtPsswd'].close()
    },
    send () {
      if (!(this.verif_mail(this.email, 'connection-id') &&
            this.verif_password(this.password, 'connection-password'))) return
      this.$http.post(apiRoot + 'auth/signIn', {
        user_email: this.email,
        user_passwd: this.password
      }).then((response) => {
        this.clearUserStore()
        this.initUserStore()
        this.initProfilesStore()
        this.$router.push('/user/profile')
      }, (response) => {
        this.clearUserStore()
        switch (response.status) {
          case 200:
            console.log('User connected')
            break
          case 400:
            this.error_message = 'Erreur de connexion. Veuillez ressayer plus tard.'
            break
          case 401:
            this.error_message = 'Votre compte n\'est pas activé.'
            break
          case 404:
            document.getElementById('connection-id').classList.add('md-input-invalid')
            break
          case 409:
            document.getElementById('connection-password').classList.add('md-input-invalid')
            break
          default:
            console.log('Unknown error')
        }
      })
    }
  }
}
</script>

<style lang="css">
.error-msg {
  color: red;
  font-weight: bold;
}

#connectionForm p {
  font-size: x-small;
  color: darkgray;
  text-align: center;
}
</style>
