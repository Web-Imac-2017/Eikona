import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    urlBeforeConnexion: '/'
  },
  mutations: {
    SET_CURRENT_URL: (state, url) => { state.urlBeforeConnexion = url }
  },
  getters: {
    url: state => { return state.urlBeforeConnexion }
  }
})
