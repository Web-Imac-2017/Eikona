<template lang="html">
  <md-layout tag="form" novalidate @submit.stop.prevent="addNewProfile">
    <md-input-container id="profile-creation-name">
      <label>Nom du profil</label>
      <md-input v-model="profile.name" required></md-input>
      <span class="md-error">Ce nom de profil est déjà utilisé.</span>
    </md-input-container>
    <md-input-container id="profile-creation-desc">
      <label>Description</label>
      <md-textarea v-model="profile.desc"></md-textarea>
      <span class="md-error">Votre message comporte des caractères non autorisés.</span>
    </md-input-container>
    <md-switch v-model="profile.isPrivate" id="privateSwitch1" name="privateSwitch" class="md-primary">Rendre ce profil visible des autres utilisateurs.</md-switch>
    <md-button type="submit" @click.native="addProfile" class="md-raised md-primary">
      Ajouter ce profil
    </md-button>
  </md-layout>
</template>

<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import formVerifications from './../formVerifications.js'

export default {
  name: 'profile-creation',
  store: store,
  data () {
    return {
      profile: {
        name: '',
        desc: '',
        isPrivate: false
      }
    }
  },
  mixins: [formVerifications],
  methods: {
    ...Vuex.mapActions({
      addProfileStore: 'addProfile'
    }),
    addProfile () {
      if (!(this.verif_name(this.profile.name, 'profile-creation-name') &&
            this.verif_text(this.profile.desc, 'profile-creation-desc'))) return
      var value = {
        profileName: this.profile.name
      }
      if (this.profile.desc != '') value = {...value, profileDesc: this.profile.desc}
      if (this.profile.isPrivate) value = {...value, profilePrivate: 0}
      this.$http.post('/Eikona/do/profile/create', value).then((response) => {
        console.log('SUCCESS: profile creation', response)
        this.addProfileStore(response.data.data.profileID)
      }, (response) => {
        console.error('ERR: profile creation request', response)
        switch (response.status) {
          case 400:
            console.log('Bad request')
            break
          case 409:
            console.log('Conflict')
            document.getElementById('profile-creation-name').classList.add('md-input-invalid')
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
</style>
