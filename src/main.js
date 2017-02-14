// The following line loads the standalone build of Vue instead of the runtime-only build,
// so you don't have to do: import Vue from 'vue/dist/vue'
// This is done with the browser options. For the config, see package.json
import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'

Vue.use(Vuex)
Vue.use(VueRouter)

const router = new VueRouter({
	mode : 'history',
	routes: [{
		path: '/',
		component: require('./components/Hello.vue')
	}, {
		path:'*',
		redirect: '/'
	}]
})

new Vue({ // eslint-disable-line no-new
  el: '#app',
	router,
  render: (h) => h(require('./App.vue'))
})
