<template>
  <div id="app">
    <topHeader></topHeader>
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>
import topHeader from './components/Header.vue'
import mainFooter from './components/MainFooter.vue'
import post from './components/Post.vue'
export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter,
    post
  }
}
</script>

<style lang="css" scoped>
</style>
