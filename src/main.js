// The following line loads the standalone build of Vue instead of the runtime-only build,
// so you don't have to do: import Vue from 'vue/dist/vue'
// This is done with the browser options. For the config, see package.json
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VueResource from 'vue-resource'
import VueMaterial from 'vue-material'

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VueResource)
Vue.use(VueMaterial)


// Configuration VueRouter
const scrollBehavior = (to, from, savedPosition) => {
  if (savedPosition) {
    // savedPosition is only available for popstate navigations.
    return savedPosition
  } else {
    const position = {}
    // new navigation.
    // scroll to anchor by returning the selector
    if (to.hash) {
      position.selector = to.hash
    }
    // check if any matched route config has meta that requires scrolling to top
    if (to.matched.some(m => m.meta.scrollToTop)) {
      // cords will be used if no selector is provided,
      // or if the selector didn't match any element.
      position.x = 0
      position.y = 0
    }
    // if the returned position is falsy or an empty object,
    // will retain current scroll position.
    return position
  }
}

const router = new VueRouter({
  mode: 'history',
  base: '/Eikona',
  scrollBehavior,
  routes: [{
    path: '',
    component: require('./components/Home.vue')
  },
  {
    path: '/search/:type/:query',
    component: require('./components/Search-page.vue'),
    props: true
  },
  {
    path: '/user',
    component: require('./components/MainPage.vue')
  },
  {
    path: '/user/profile',
    component: require('./components/Profil-selection.vue')
  },
  {
    path: '/user/settings',
    component: require('./components/Settings.vue')
  },
  {
    name: 'profile_name',
    path: '/Eikona/:profileID',
    component: require('./components/ProfilePage.vue')
  },
  {
    path: '*',
    redirect: ''
  }]
})


// Configuration VueResource
Vue.http.options.emulateHTTP = true
Vue.http.options.emulateJSON = true

// configuration couleur vue Material
Vue.material.registerTheme('default', {
  primary: {
    color: 'cyan',
    hue: 800
  }
}) 


new Vue({ // eslint-disable-line no-new
  el: '#app',
  router: router,
  render: (h) => h(require('./App.vue'))
})
