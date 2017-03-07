import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    user: {
      id: 0,
      email: '',
      connected: false
    },
    profiles: [],
    currentProfile: -1
  },
  mutations: {
    SET_USER: (state, id, email, isConnected) => {
      state.user.id = id
      state.user.email = email
      state.user.connected = isConnected
      console.log('Ajout au store de user')
    },
    SET_LIST_PROFILES: (state) => {
      Vue.http.post('/Eikona/do/user/profiles/', {}).then((response) => {
        for (var profile in response.data) {
          state.profiles.push(profile)
        }
      }, (response) => {
        console.log('ERR: récupération des profils', response)
      })
    },
    SET_CURRENT_PROFILE: (state, index) => {
      state.currentProfile = index
    }
  },
  getters: {
    user: state => {
      if (state.user.connected) {
        return state.user
      }
      return false
    }
  },
  strict: true
})
