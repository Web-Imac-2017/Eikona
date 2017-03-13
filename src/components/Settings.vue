<template lang="html">
  <md-layout>
    <md-layout md-flex="25" md-flex-small="100" md-align="center">
      <md-whiteframe md-elevation="8" id="settings-user">
        <form @submit.stop.prevent="valid('confirmActual')">
          <md-input-container id="settings-user-name">
            <label>Nom du compte</label>
            <md-input v-model="infos.name"></md-input>
            <span class="md-error">Ce nom de compte est invalide.</span>
          </md-input-container>
          <md-input-container id="settings-user-email">
            <label>Mail</label>
            <md-input v-model="infos.mail"></md-input>
            <span class="md-error">Cet email est déjà utilisé ou invalide.</span>
          </md-input-container>
          <md-input-container id="settings-user-password" md-has-password>
            <label>Mot de passe</label>
            <md-input v-model="infos.password"></md-input>
            <span class="md-error">Mot de passe invalide (au moins 8 caractères).</span>
          </md-input-container>
          <md-input-container id="settings-user-confirm" md-has-password>
            <label>Confirmation</label>
            <md-input v-model="infos.confirm"></md-input>
            <span class="md-error">Ne correspond pas au mot de passe.</span>
          </md-input-container>
          <md-button type="reset">Annuler</md-button>
          <md-button type="submit" class="md-raised md-primary">Modifier mes informations</md-button>
        </form>
        <md-dialog-confirm
          :md-title="confirm.title"
          :md-content="confirm.content"
          :md-ok-text="confirm.ok"
          :md-cancel-text="confirm.cancel"
          @close="editInfos"
          ref="confirmActual">
        </md-dialog-confirm>
      </md-whiteframe>
    </md-layout>
    <!-- Afficher les editions des profils -->
  </md-layout>
</template>

<script>
import apiRoot from './../config.js'
import formVerification from './../formVerifications.js'
import store from './connectionStore.js'
import Vuex from 'vuex'

export default {
  name: 'settings',
  store: store,
  data () {
    return {
      confirm: {
        title: 'Confirmation de modification de compte',
        content: 'Êtes-vous sûr de vouloir modifier ces informations ?',
        ok: 'Confirmer',
        cancel: 'Annuler'
      },
      infos: {
        name: null,
        mail: null,
        password: null,
        confirm: null
      }
    }
  },
  mixins: [formVerification],
  computed: {
    ...Vuex.mapGetters([
      'getUser'
    ])
  },
  methods:{
    ...Vuex.mapActions([
      'updateUser'
    ]),
    valid (ref) {
      this.$refs[ref].open()
    },
    editInfos (isOk) {
      if (isOk !== 'ok' ||
      (this.infos.name !== null && !this.verif_name(this.infos.name, 'settings-user-name')) ||
      (this.infos.mail !== null && !this.verif_mail(this.infos.mail, 'settings-user-mail')) ||
      (this.infos.password !== null && !this.verif_password(this.infos.password, 'settings-user-password')) ||
      (this.infos.password !== null && (this.infos.confirm === null || !this.verif_confirm(this.infos.password, this.infos.confirm, 'settings-user-confirm')))) return

      if (this.infos.name !== null) editName (this.infos.name)
      if (this.infos.mail !== null) editMail (this.infos.mail)
      if (this.infos.password !== null) editPassword (this.infos.password, this.infos.confirm)
    },
    editName (name) {
      this.$http.post(apiRoot + 'user/edit/NAME', {name: name}).then(response => {
        var u = this.getUser
        u.userName = response.data.data.userName
        this.updateUser(u)
      }, response => {
        switch (response.status) {
          case 400:
            console.error('Bad request', response);
            break
          case 409:
            console.error('Bad name', response);
            break
        }
      })
    },
    editMail (mail) {
      this.$http.post(apiRoot + 'user/edit/EMAIL', {email: mail}).then(response => {
        var u = this.getUser
        u.userEmail = response.data.data.userEmail
        this.updateUser(u)
      }, response => {
        switch (response.status) {
          case 400:
            console.error('Bad request', response);
            break
          case 409:
            console.error('Bad mail', response);
            break
        }
      })
    },
    editPassword (password, confirm) {
      this.$http.post(apiRoot + 'user/edit/PASSWORD', {passwd: password, passwd_confirm: confirm}).then(response => {}, response => {
        switch (response.status) {
          case 400:
            console.error('Bad request', response);
            break
          case 409:
            console.error('Bad password', response);
            break
        }
      })
    }
  }
}
</script>

<style lang="css">
#settings-user {
  padding : 50px;
  margin: 5%;
  text-align: center;
}
</style>
