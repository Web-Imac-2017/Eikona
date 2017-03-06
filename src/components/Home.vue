<template lang="html">
  <div id="home">
    <div class="angled_container angled_container--open-right">
      <md-layout :md-gutter="8" md-align="center">
      <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33">
        <description id="description" class="home-top-element"></description>
      </md-layout>
      <md-layout md-flex-xsmall="100" md-flex-small="50" md-flex-medium="33">
        <connection id="connect" class="home-top-element"></connection>
      </md-layout>
      </md-layout>
    </div>
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
  text-align: left;
  color: white;
  
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


/*********************** BACKGROUND CSS ********************/

.angled_container {
  height: 850px;
  position: relative;
  overflow: hidden;
  background-color: #fff;
}

.angled_container:before,
.angled_container:after {
  content: "";
  width: 100%; height: 100%;
  display: block;
  position: absolute;
  bottom: 0; right: 0;
}
.angled_container:before {
  background-color: #078b9c ;
  transform: skewY(30deg);
  transform-origin: right bottom;

}

.angled_container:after {
  background-color: #226986;
  transform: skewY(-30deg);
  transform-origin: left bottom;

}

</style>
