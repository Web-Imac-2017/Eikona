<template lang="html">
  <md-layout md-flex="50" md-flex-small="100" md-align="center">
    <md-whiteframe md-elevation="8" id="profile-selection">
      <h1>Bonjour {{ getUser.userName }} !</h1>
      <p>Veuillez sélecionner un profil :</p>
      <md-list class="md-double-line">
        <profile v-for="(item, index) in profiles" :profile="item" :index="index" :extended="true" @select="select(id)"></profile>
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
    ...Vuex.mapActions([
      'selectProfile'
    ]),
    createProfile () {
      this.creationForm = true
    },
    select (id) {
      this.selectProfile(id)
      // redirection vers page correspondante
    }
  }
}
</script>

<style lang="css">
#profile-selection {
  padding : 50px;
}
</style>
