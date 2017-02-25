<template>
  <div id="app">
    <topHeader></topHeader>
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>

import topHeader from './components/Header.vue'
import mainFooter from './components/mainFooter.vue'

export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter
  }

}
</script>

<style lang="css" scoped>
</style>s
