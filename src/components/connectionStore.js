import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: {
      userID: null
    },
    userId: 'none',
    connected: false
  },
  mutations: {
    SET_USER: (state, id) => { state.userId = id },
    CONNECT: (state, connection) => { state.connected = connection}
  },
  getters: {
    userId: state => { return state.userId },
    isConnected: state => { return state.connected },
    getUser: state => state.user
  }
})
