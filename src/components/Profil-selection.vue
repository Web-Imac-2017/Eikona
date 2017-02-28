<template lang="html">
  <md-layout md-flex="50" md-flex-small="100" md-align="center">
    <md-whiteframe md-elevation="8">
      <h1>Bonjour {{ user.id }} !</h1>
      <p>Veuillez sélecionner un profil :</p>
      <md-layout md-column md-align="center">
        <profile v-for="(profile, i) in profiles" :profile="profile" :i="i"></profile>
        <md-layout>
          <md-button type="button" @click.native="add_profile">
            <md-layout md-flex="100">
              <md-layout md-flex="25">
                <md-avatar class="md-avatar-icon">
                  <md-icon>add</md-icon>
                </md-avatar>
              </md-layout>
              <md-layout md-flex="75">
                <h3>Ajouter un profil</h3>
              </md-layout>
            </md-layout>
          </md-button>
        </md-layout>
      </md-layout>
    </md-whiteframe>
  </div>
</template>

<script>
import store from './connectionStore.js'
import profile from './Profile.vue'

export default {
  name: 'profile-selection',
  store: store,
  components: {
    profile
  },
  computed: {
    user () {
      return this.$store.state.user
    },
    profiles () {
      return this.$store.state.profiles
    }
  },
  methods: {
    add_profile () {
      console.log('add_profile')
      // redirection vers la page de création de profil
    }
  }
}
</script>

<style lang="css">
</style>
