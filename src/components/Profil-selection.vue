<template lang="html">
  <div>
    <md-whiteframe md-elevation="8">
      <h1>Bonjour {{ user.id }} !</h1>
      <p>Veuillez sélecionner un profile :</p>
      <profile v-for="(profile, index) in profiles"></profile>
      <md-layout>
        <md-layout><md-button class="md-icon-button md-raised md-primary"><md-icon>add</md-icon></md-button></md-layout>
        <md-layout>
          <h3>Ajouter un profil</h3>
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
  computed:{
    user () {
      return this.$store.state.user
    },
    profiles () {
      return this.$store.state.profiles
    }
  }
}
</script>

<style lang="css">
</style>
