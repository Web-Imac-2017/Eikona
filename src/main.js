// The following line loads the standalone build of Vue instead of the runtime-only build,
// so you don't have to do: import Vue from 'vue/dist/vue'
// This is done with the browser options. For the config, see package.json
import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';

Vue.use(VueRouter);
Vue.use(Vuex);

const router = new VueRouter({
  mode : "history",
  routes : [{
    path: '/',
    component : require('./components/Hello.vue')
  }, {
    path: '*',
    redirect : '/'
  }]
});

new Vue({ // eslint-disable-line no-new
  el: '#app',

  render: (h) => h(require('./App.vue'))
});
