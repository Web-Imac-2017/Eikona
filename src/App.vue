<template>
  <div id="app">
    <topHeader></topHeader>
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>

let topHeader = require('./components/Header.vue')
let mainFooter = require('./components/mainFooter.vue')


export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter
   
  }

}
</script>

<style lang="css" scoped>
</style>
