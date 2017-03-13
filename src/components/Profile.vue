<template lang="html">
    <md-list-item>
      <md-avatar>
        <img :src="profile.profilePict" alt="Avatar"/>
      </md-avatar>

      <div class="md-list-text-container">
        <span>{{ profile.profileName }}</span>
        <p v-show="extended">{{ profile.nbrPosts}} posts - {{ profile.nbrFollowings }} abonnements - {{ profile.nbrFollowers }} abonnés</p>
        <p>{{profile.profile_desc}}</p>
      </div>

      <md-button class="md-icon-button md-list-action" @click.native="select">
        <md-icon v-if="activeProfile">radio_button_checked</md-icon>
        <md-icon v-else>radio_button_unchecked</md-icon>
      </md-button>

      <md-divider class="md-inset"></md-divider>
    </md-list-item>
</template>

<script>
import Vuex from 'vuex'
import store from './connectionStore.js'

export default {
  name: 'profile',
  store: store,
  props: [
    'profile',
    'index',
    'extended'
  ],
  computed: {
    ...Vuex.mapGetters([
      'currentProfileIndex'
    ]),
    activeProfile () {
      return this.index === this.currentProfileIndex
    }
  },
  methods: {
    select () {
      this.$emit('select', this.profile.profileID)
    }
  }
}
</script>

<style lang="css" scoped>
.profile-subtitles {
  font-size: 1vw;
}

</style>
