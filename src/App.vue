<template>
  <div id="app">
    <topHeader></topHeader>
    <Post></Post>
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>

let topHeader = require('./components/Header.vue')
let mainFooter = require('./components/mainFooter.vue')
import Post from './components/Post.vue'


export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter,
    Post
   
  }

}
</script>

<style lang="css" scoped>
</style>s
