<template lang="html">
  <md-layout tag="form" novalidate @submit.stop.prevent="addNewProfile">
    <md-input-container id="profile-creation-name">
      <label>Nom du profil</label>
      <md-input v-model="profile.profileName" required></md-input>
      <span class="md-error" v-if="error">{{ error_msg }}</span>
    </md-input-container>
    <md-input-container>
      <label>Description</label>
      <md-textarea v-model="profile.profileDesc"></md-textarea>
    </md-input-container>
      <md-switch v-model="profile.profilePrivate" id="privateSwitch1" name="privateSwitch" class="md-primary">Rendre ce profil visible des autres utilisateurs.</md-switch>
    <md-button type="submit" @click.native="addNewProfile" class="md-raised md-primary">
      Ajouter ce profil
    </md-button>
  </md-layout>
</template>

<script>
export default {
  name: 'profile-creation',
  data () {
    return {
      profile: {
        profileName: '',
        profileDesc: '',
        profilePrivate: false
      },
      error: false,
      error_msg: 'Ce nom de profil est déjà utilisé.'
    }
  },
  methods: {
    addNewProfile () {
      this.$http.post('/Eikona/do/profile/create', this.profile).then((response) => {
        console.log('SUCCESS: profile creation', response.data)
        this.$router.push('/Eikona/user/profile')
      }, (response) => {
        console.error('ERR: profile creation request', response)
        switch (response.status) {
          case 400:
            console.log('Bad request')
            break
          case 409:
            console.log('Conflict')
            this.error = true
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
