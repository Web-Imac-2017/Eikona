<template lang="html">
  <div id="home">
  	<informationsProfilAutre></informationsProfilAutre>
  </div>
</template>

<script>
import connectionForm from './Connection.vue'
import informationsProfilAutre from './Informations-profil.vue'

export default {
  name: 'home',
  components: {
    connectionForm,
    informationsProfilAutre
  }
}
</script>

<style lang="css">
</style>
