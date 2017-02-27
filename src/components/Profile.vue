<template lang="html">
  <md-layout md-align="center" @click="select">
    <md-layout><md-avatar><img :src="profile.avatarPath" alt="Avatar"/></md-avatar></md-layout>
    <md-layout md-column>
      <md-layout>
        <h3>{{ profile.id }}</h3>
      </md-layout>
      <md-layout>
        <p>
          <span>{{ profile.publication }} publications</span> -
          <span>{{ profile.followers }} abonnés</span> -
          <span>{{ profile.followings }} abonnements</span>
        </p>
      </md-layout>
    </md-layout>
  </md-layout>
</template>

<script>
export default {
  name: 'profile',
  props: ['profile'],
  methods: {
    select () {
      // selection profile ajax
      // redirection vers page correspondante
    }
  }
}
</script>

<style lang="css">
</style>
