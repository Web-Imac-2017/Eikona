// The following line loads the standalone build of Vue instead of the runtime-only build,
// so you don't have to do: import Vue from 'vue/dist/vue'
// This is done with the browser options. For the config, see package.json
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VueResource from 'vue-resource'

Vue.use(VueRouter, Vuex, VueResource)

const router = new VueRouter({
  mode: 'history',
  routes: [{
    path: '/',
    component: require('./components/Home.vue')
  }, {
    path: '/user/:id/settings',
    component: require('./components/Settings.vue')
  },
  {
    path: '*',
    redirect: '/'
  }]
})

new Vue({ // eslint-disable-line no-new
  el: '#app',
  router,
  render: (h) => h(require('./App.vue'))
})
