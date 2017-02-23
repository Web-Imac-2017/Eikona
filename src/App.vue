<template>
  <div id="app">
    <topHeader></topHeader>
    <router-view></router-view>
  </div>
</template>

<script>
let topHeader = require('./components/Header.vue')

export default {
  name: 'app',
  components: {
    topHeader
  }
}
</script>

<style lang="css" scoped>
</style>
