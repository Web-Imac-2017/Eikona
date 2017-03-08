<template lang="html">
  <md-layout>
    <form @submit.stop.prevent="">
      <div v-if="!hasCode">
        <md-input-container id="resetPassword-mail">
          <label>Adresse mail</label>
          <md-input type="email" v-model="email"></md-input>
          <span class="md-error">Adresse invalide ou inconnue</span>
        </md-input-container>
        <md-button class="md-accent md-mini md-raised" @click.native="getCode">Demander un code de régénération du mot de passe</md-button>
      </div>
      <div v-else>
        <md-input-container md-has-password id="resetPassword-password">
          <label>Nouveau mot de passe</label>
          <md-input type="password" v-model="password"></md-input>
    			<span class="md-error">Mot de passe invalide (8 caractères minimum)</span>
        </md-input-container>
        <md-input-container md-has-password id="resetPassword-confirm">
          <label>Confirmation du nouveau mot de passe</label>
          <md-input type="password" v-model="confirm"></md-input>
    			<span class="md-error">Le mot de passe ne correspond pas</span>
        </md-input-container>
        <md-input-container md-has-password id="resetPassword-code">
          <label>Code de régénération</label>
          <md-input v-model="code"></md-input>
    			<span class="md-error">Code invalide</span>
        </md-input-container>
        <md-button class="md-accent md-mini md-raised" @click.native="resetPassword">Réinitialiser le mot de passe</md-button>
      </div>
      <md-dialog-alert
        :md-content="alert.content"
        :md-ok-text="alert.ok"
        @close="onClose"
        ref="alert">
      </md-dialog-alert>
    </form>
  </md-layout>
</template>

<script>
import formVerifications from './../formVerifications.js'

export default {
  name: 'resetPassword',
  data () {
    return {
      hasCode: false,
      email: '',
      password: '',
      confirm: '',
      code: '',
      alert: {
        content: 'Reinitialisation du mot de passe',
        ok: 'Ok'
      }
    }
 },
 mixins: [formVerifications],
 methods: {
   verif_code (value, inputContainerId) {
     var regex = /[a-zA-Z0-9]{6}$/
     if(!regex.test(value)){
       document.getElementById(inputContainerId).classList.add('md-input-invalid')
       return false
     } else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
     return true
   },
   onClose () {
     if (!this.hasCode) this.$emit('close')
   },
   getCode () {
     if(!this.verif_mail(this.email, 'resetPassword-mail')) return
     this.$http.post('/Eikona/do/auth/forgottenPassword/', {
       user_email: this.email
     }).then((response) => {
       console.log(response)
       this.alert.content = 'Un code de validation vous a été envoyé par mail'
       this.hasCode = true
       this.$refs['alert'].open()
     }, (response) => {
       console.log(response)
       switch (response.status) {
         case 400:
         case 404:
          document.getElementById('resetPassword-mail').classList.add('md-input-invalid')
          break
         default:
           console.log('Unknown error', response)
       }
     })
   },
   resetPassword () {
     if (!(this.verif_password(this.password, 'resetPassword-password') &&
           this.verif_confirm(this.confirm, this.password, 'resetPassword-confirm') &&
           this.verif_code(this.code, 'resetPassword-code'))) return

      this.$http.post('/Eikona/do/auth/regenere/', {
        user_email: this.email,
        user_passwd: this.password,
        user_passwd_confirm: this.confirm,
        code: this.code
      }).then((response) => {
        console.log(response)
        this.alert.content = 'Votre mot de passe a été réinitialisé'
        this.hasCode = false
        this.$refs['alert'].open()
      }, (response) => {
        console.log(response)
        switch (response.status) {
          case 400:
            this.verif_password(this.password, 'resetPassword-password')
            this.verif_confirm(this.confirm, this.password, 'resetPassword-confirm')
            this.verif_code(this.code, 'resetPassword-code')
           break
          case 404:
            document.getElementById('resetPassword-mail').classList.add('md-input-invalid')
            break
          case 409:
            document.getElementById('resetPassword-password').classList.add('md-input-invalid')
            document.getElementById('resetPassword-confirm').classList.add('md-input-invalid')
            document.getElementById('resetPassword-code').classList.add('md-input-invalid')
            break
          default:
            console.log('Unknown error', response)
        }
      })
   }
 }
}
</script>

<style lang="css">
</style>
