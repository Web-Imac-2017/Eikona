<template lang="html">
  <form id="connectionForm" @submit.stop.prevent="send">
    <h3>Vous avez déjà un compte ?</h3>
    <div v-if="error_message != ''" class="error-msg">{{ error_message }}</div>
    <md-input-container>
      <label>Email</label>
      <md-input id="connection-id" required type="email" v-model="user_email"></md-input>
      <span v-if="error_mail" class="md-error">Adresse mail inconnue ou incorrecte</span>
    </md-input-container>
    <md-input-container md-has-password>
      <label>Mot de passe</label>
      <md-input id="connection-password" required type="password" v-model="user_passwd"></md-input>
      <span v-if="error_password" class="md-error">Mot de passe incorrect</span>
    </md-input-container>
    <p>Les champs marqués d'un * sont obligatoires.</p>
    <md-button  class="md-primary md-raised" type="submit">SE CONNECTER</md-button>
  </form>
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
      error_message: '',
      forgetPassword: false
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
    send () {
      if (!(this.verif_mail(this.email, 'connection-id') && this.verif_password(this.password, 'connection-password'))) return
      this.$http.post(apiRoot + 'auth/signIn', {
        user_email: this.email,
        user_passwd: this.password
      }).then((response) => {
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
