<template lang="html">
    <md-list-item>
      <md-avatar>
        <img :src="profile.avatarPath" alt="Avatar"/>
      </md-avatar>

      <div class="md-list-text-container">
        <span>{{ profile.profile_name }}</span>
        <p v-show="extended">{{ profile.profile_views }} posts - {{ profile.profile_views }} abonnements - {{ profile.profile_views }} abonnés</p>
      </div>

      <md-button class="md-icon-button md-list-action" @click.native="select">
        <md-icon v-if="activeProfile">radio_button_checked</md-icon>
        <md-icon v-else>radio_button_unchecked</md-icon>
      </md-button>

      <md-divider class="md-inset"></md-divider>
    </md-list-item>
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
      return (this.$store.state.currentProfile === this.i)
    }
  },
  methods: {
    select () {
      this.$emit('select', this.profile)
    }
  }
}
</script>

<style lang="css">
.profile-subtitles {
  font-size: 1vw;
}

</style>
