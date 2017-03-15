<template lang="html">
  <md-layout id="profile-selection-container" md-flex="50" md-flex-small="100" md-align="center">
    <md-whiteframe md-elevation="8" id="profile-selection">
      <h1>Bonjour {{ getUser.userName }} !</h1>
      <p>Veuillez sélecionner un profil :</p>
      <md-list class="md-triple-line">
        <profile v-for="(item, i) in profiles" :profile="item" :key="item" :index="i" :extended="true" @select="select"></profile>
        <md-list-item class="md-inset">
          <span>Ajouter un profil</span>
          <md-button id="profilCreation-button" @click.native="createProfile('dialog')" class="md-icon-button md-list-action">
            <md-icon class="md-accent">add_circle</md-icon>
          </md-button>
        </md-list-item>
      </md-list>
    </md-whiteframe>
    <md-dialog md-open-from="profilCreation-button" md-close-to="profilCreation-button" ref="dialog">
      <md-dialog-title>Nouveau profil</md-dialog-title>
      <md-dialog-content>
        <profileCreation @close="closeCreation('dialog')"></profileCreation>
      </md-dialog-content>
    </md-dialog>
  </md-layout>
</template>

<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import profile from './Profile.vue'
import profileCreation from './Profile-creation.vue'

export default {
  name: 'profile-selection',
  store: store,
  components: {
    profile,
    profileCreation
  },
  computed: {
    ...Vuex.mapGetters([
      'getUser',
      'profiles'
    ])
  },
  methods: {
    ...Vuex.mapActions([
      'selectProfile'
    ]),
    createProfile (ref) {
      this.$refs[ref].open()
    },
    closeCreation (ref) {
      this.$refs[ref].close()
    },
    select (profileId) {
      this.selectProfile(profileId)
      this.$router.replace('/user')
    }
  }
}
</script>

<style lang="css">
#profile-selection-container {
  min-height: 100vh;
  padding: 5%;
  background-image: url('./../assets/bg_compte.jpg');
  background-size: cover;
  background-position: center center;
}
#profile-selection {
  padding: 50px;
  background-color: white;
  text-align: center;
}
#profile-selection > * {
  text-align: center;
}
</style>
