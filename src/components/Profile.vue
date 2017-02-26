<template lang="html">
  <md-layout @click="select">
    <md-layout><md-avatar><img :src="avatarPath" alt="Avatar"/></md-avatar></md-layout>
    <md-layout>
      <h3>{{ profile.id }}</h3>
      <p>
        <span>{{ profile.publication }} publications</span> -
        <span>{{ profile.followers }} abonnés</span> -
        <span>{{ profile.followings }} abonnements</span>
      </p>
    </md-layout>
  </md-layout>
</template>

<script>
export default {
  name: 'profile',
  props: ['profile'],
  datas () {
    return {
      avatarPath: this.profile.avatarPath
    }
  },
  methods : {
    select () {
      //selection profile ajax
      //redirection vers page correspondante
    }
  }
}
</script>

<style lang="css">
</style>
