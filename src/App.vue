<template>
  <div id="app">
    <topHeader></topHeader>
    <post ></post>    
    <router-view></router-view>
    <MainFooter></MainFooter>
  </div>
</template>

<script>

let topHeader = require('./components/Header.vue')
let MainFooter = require('./components/MainFooter.vue')
import post from './components/Post.vue'


export default {
  name: 'app',
  components: {
    topHeader,
    MainFooter,
    post
   
  }

}
</script>

<style lang="css" scoped>
</style>s
