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
import apiRoot from './config.js'
import store from './components/connectionStore.js'
import Vuex from 'vuex'

export default {
  name: 'app',
  store: store,
  components: {
    topHeader,
    mainFooter,
    post
  },
  mounted () {
    this.$http.get(apiRoot + 'auth/signIn/').then(function (response) {
      this.clearUser()
      this.clearProfiles()
      this.initUser()
      this.initProfiles()
      this.$router.push('/user')
    }, function (response) {
      console.log('No cookies', response);
    })
  },
  methods: {
    ...Vuex.mapActions([
      'initUser',
      'clearUser',
      'initProfiles',
      'clearProfiles'
    ])
  }
}
</script>

<style lang="css" scoped>
</style>
