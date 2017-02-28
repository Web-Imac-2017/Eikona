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
      posts: 4,
      followers: 6,
      followings: 10
    },
    {
      id: 'Jean Michel',
      avatarPath: './assets/Eiko.png',
      posts: 5,
      followers: 6,
      followings: 0
    },
    {
      id: 'Jean Jean',
      avatarPath: './assets/Eiko.png',
      posts: 0,
      followers: 2,
      followings: 10
    }],
    currentProfile: 0
  },
  mutations: {
    SET_USER: (state, id, email, isConnected) => {
      state.user.id = id
      state.user.email = email
      state.user.connected = isConnected
    },
    SET_LIST_PROFILES: (state) => {
      this.$http.post('/Eikona/do/user/profiles/', {}).then((response) => {
        for (profile in response.data) {
          state.profiles.push(profile);
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
