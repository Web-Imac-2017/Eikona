import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    user: {
      id: 'jean99',
      email: 'jean.michel@gmail.com',
      connected: true
    },
    // profiles de tests, reel à recupérer en fonction de l'utilisateur co
    profiles: [{
      id: 'Jean Jean',
      avatarPath: './assets/Eiko.png',
      publications: 4,
      followers: 6,
      followings: 10
    },
    {
      id: 'Jean Michel',
      avatarPath: './assets/Eiko.png',
      publications: 5,
      followers: 6,
      followings: 0
    },
    {
      id: 'Jean Jean',
      avatarPath: './assets/Eiko.png',
      publications: 0,
      followers: 2,
      followings: 10
    }]
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
      if (state.user.connected) {
        return state.user
      }
      return false
    }
  },
  strict: true
})
