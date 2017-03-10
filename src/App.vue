<template>
  <div id="app">
    <topHeader></topHeader>
    <router-view></router-view>
    <MainFooter></MainFooter>
  </div>
</template>

<script>
import topHeader from './components/Header.vue'
import mainFooter from './components/MainFooter.vue'

export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter
  }
}
</script>
