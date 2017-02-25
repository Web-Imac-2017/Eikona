<template lang="html">
  <div id="home">
    <description id="description" class="home-top-element"></description>
    <connection id="connect" class="home-top-element"></connection>
    <md-whiteframe md-elevation="8" id="register"><inscription></inscription></md-whiteframe>
  </div>
</template>

<script>
import connection from './Connection.vue'
import inscription from './Inscription.vue'
import description from './Home-description.vue'

export default {
  name: 'home',
  components: {
    connection,
    inscription,
    description
  }
}
</script>

<style lang="css">
#home {
  text-align: center;
}
#description {
  width: 40%;
  text-align: left;
  color: white;
  background-image: url(./../assets/arbre_violet.jpeg);
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
}
#connect {
  width: 40%;
  text-align: right;
}
.home-top-element {
  display: inline-block;
  vertical-align: top;
  z-index: 1;
}
#register {
 max-width: 40%;
 padding: 50px 100px;
 text-align: center;
 margin: 50px auto;
}

</style>
