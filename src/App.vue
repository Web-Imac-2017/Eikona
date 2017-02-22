<template>
  <div id="app">
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>

let mainFooter = require('./components/mainFooter.vue');

export default {
  name: 'app',
  components : { mainFooter }
}
</script>
