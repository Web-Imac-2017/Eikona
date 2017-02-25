<template lang="html">
  <form id="connectionForm" method="post">
    <h1>Connectez vous</h1>
    <md-input-container>
      <label>E-mail</label>
      <md-input type="email" v-model="user_email"></md-input>
    </md-input-container>
    <md-input-container md-has-password>
      <label>Mot de passe</label>
      <md-input type="password" v-model="user_passwd"></md-input>
    </md-input-container>
    <md-button  class="md-raised md-primary" @click="send">Se Connecter</md-button>
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
      user_passwd: ''
    }
  },
  methods: {
    send () {
      // vérifer validitée des champs
      console.log('Send : ' + this.user_email + '  ' +  this.user_passwd)
      this.$http.post('/do/auth/signIn/', {
        user_email: this.user_email,
        user_passwd: this.user_passwd
      }).then((response) => {
        console.log('Connected', response)
        store.commit('SET_USER', response.data.userID)
      }, (response) => {
        console.log('Not connected', response)
        switch(response.code){
          case 400:
            console.log('Bad request')
            break
          case 401:
            console.log('Unauthorized')
            break
          case 404:
            console.log('Not found')
            break
          case 409:
            console.log('Conflict')
            break
        }
      })
    }
  }
}
</script>

<style lang="css" scoped>
  #connectionForm {
    width : 250px;
    text-align: right;
  }
</style>
