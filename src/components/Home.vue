<template lang="html">
  <div id="home">
  	<connectionForm></connectionForm>
  	<informationsProfil></informationsProfil>
  </div>
</template>

<script>
import connectionForm from './Connection.vue'
import informationsProfil from './Informations-profil.vue'

export default {
  name: 'home',
  components: {
    connectionForm,
    informationsProfil
  }
}
</script>

<style lang="css">
</style>
