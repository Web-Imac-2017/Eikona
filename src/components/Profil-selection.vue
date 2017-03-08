<template lang="html">
  <md-layout md-flex="50" md-flex-small="100" md-align="center">
    <md-whiteframe md-elevation="8" id="profile-selection">
      <h1>Bonjour {{ getUser.userName }} !</h1>
      <p>Veuillez sélecionner un profil :</p>
      <md-list class="md-double-line">
        <profile v-for="(profile, i) in profiles" :profile="profile" :i="i" @select="select(selectProfile)"></profile>
        <md-list-item class="md-inset">
          <span>Ajouter un profil</span>
          <md-button @click.native="createProfile" class="md-icon-button md-list-action">
            <md-icon class="md-accent">add_circle</md-icon>
          </md-button>
        </md-list-item>
      </md-list>
    </md-whiteframe>
    <profileCreation v-if="creationForm"></profileCreation>
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
  data () {
    return {
      creationForm: false
    }
  },
  computed: {
    ...Vuex.mapGetters([
      'getUser',
      'profiles'
    ])
  },
  methods: {
    createProfile () {
      this.creationForm = true
    },
    select (profile) {
      console.log('select profile')

      // redirection vers page correspondante
      this.$store.commit('SET_CURRENT_PROFILE', this.i)

      // selection profile ajax
      this.$http.get('/Eikona/do/profile/setCurrent/' + this.$store.state.profiles[this.i]).then((response) => {
        console.log('Changement de profile :' + this.$store.state.profiles[this.i], response)
        // redirect vers la page du profile correspondant
        }, (response) => {
          console.log('ERR profile selection: ', response)
      })
    }
  }
}
</script>

<style lang="css">
#profile-selection {
  padding : 50px;
}
</style>
