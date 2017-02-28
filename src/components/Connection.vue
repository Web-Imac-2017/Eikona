<template lang="html">
  <form id="connectionForm" @submit.stop.prevent="send">
    <h2>Connectez vous</h2>
    <div v-if="error_message != ''" class="error-msg">{{ error_message }}</div>
    <md-input-container>
      <label>E-mail</label>
      <md-input id="connection-id" required type="email" v-model="user_email"></md-input>
      <span v-if="error_mail" class="md-error">Adresse mail inconnue ou incorrecte</span>
    </md-input-container>
    <md-input-container md-has-password>
      <label>Mot de passe</label>
      <md-input id="connection-password" required type="password" v-model="user_passwd"></md-input>
      <span v-if="error_password" class="md-error">Mot de passe incorrect</span>
    </md-input-container>
    <p>Les champs marqués d'un * sont obligatoires.</p>
    <md-button class="md-raised" type="submit">SE CONNECTER</md-button>
  </form>
</template>

<script>
import store from './connectionStore.js'

export default {
  name: 'connection',
  store: store,
  data () {
    return {
      user_email: '',
      user_passwd: '',
      error_mail: false,
      error_password: false,
      error_message: ''
    }
  },
  methods: {
    send () {
      console.log('Send : ' + this.user_email + '  ' +  this.user_passwd)
      this.$http.post('/Eikona/do/auth/signIn/', {
        user_email: this.user_email,
        user_passwd: this.user_passwd
      }).then((response) => {
        console.log('Connected', response)
        store.commit('SET_USER', response.data.userID, response.data.userEmail, true)
        // ouverture pop-up selection profil OU redirection vers page perso, vec pop-up choix de profil
      }, (response) => {
        console.log('Not connected', response)
        store.commit('SET_USER', '', '', false)
        switch (response.code) {
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
            document.getElementById('connection-id').className += ' md-input-invalid'
            break
          case 409:
            console.log('Conflict')
            this.error_password = true
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
}
</style>
