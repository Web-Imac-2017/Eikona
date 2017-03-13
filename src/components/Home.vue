<template lang="html">
  <div id="home">
    <md-layout :md-gutter="8" md-align="center">
      <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33">
        <description id="description" class="home-top-element"></description>
      </md-layout>
      <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33">
        <connection id="connect" class="home-top-element"></connection>
      </md-layout>
    </md-layout>
    <md-whiteframe md-elevation="8" id="register">
      <inscription></inscription>
    </md-whiteframe>
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
  text-align: left;
  color: white;
  background-image: url(./../assets/arbre_violet.jpeg);
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
}
#connect {
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
