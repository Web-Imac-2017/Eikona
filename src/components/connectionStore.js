import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: {
      id: '',
      email: '',
      connected: false
    }
  },
  mutations: {
    SET_USER: (state, id, email, isConnected) => {
      state.user.id = id
      state.user.email = email
      state.user.connected = isConnected
    }
  },
  getters: {
    user: state => {
      if(state.user.connected)
        return state.user
      return false
    }
  },
  strict: true
})
