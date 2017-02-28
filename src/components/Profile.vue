<template lang="html">
    <md-layout md-flex="100">
      <md-button type="button" @click.native="select">
        <md-layout md-align="center">
          <md-layout md-flex="25"><md-avatar><img :src="profile.avatarPath" alt="Avatar"/></md-avatar></md-layout>
          <md-layout md-flex="60">
            <h5>{{ profile.id }}</h5>
            <p class="profile-subtitles">{{ profile.posts }} publications - {{ profile.followers }} abonnés - {{ profile.followings }} abonnements</p>
          </md-layout>
          <md-layout md-flex="10">
            <md-icon v-if="activeProfile">radio_button_checked</md-icon>
            <md-icon v-else>radio_button_unchecked</md-icon>
          </md-layout>
      </md-layout>
    </md-button>
  </md-layout>
</template>

<script>
import store from './connectionStore.js'

export default {
  name: 'profile',
  store: store,
  props: ['profile', 'i'],
  data () {
    return {
      extended: true
    }
  },
  computed: {
    activeProfile () {
      return (this.$store.state.currentProfile == this.i)
    }
  },
  methods: {
    select () {
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
.profile-subtitles {
  font-size: 1vw;
}

</style>
