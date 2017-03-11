import Vue from 'vue'
import Vuex from 'vuex'
import apiRoot from './../config.js'

Vue.use(Vuex)


const state = {
  user: {
    userName: false
  },
  profiles: [],
  currentProfile: -1
}

const mutations = {
  SET_USER: (state, newUser) => state.user = newUser,
  ADD_PROFILE: (state, profile) => state.profiles.push(profile),
  DELETE_PROFILE: (state, profile) => state.profiles.filter(i => i !== profile),
  SET_CURRENT_PROFILE: (state, id) => state.profiles.filter(el => el.id === id)
}

const getters = {
  getUser: state => state.user,
  profiles: state => state.profiles,
  currentProfile: state => state.profiles[state.currentProfile],
  currentProfileIndex: state => state.currentProfile,
  getProfile: (state, index) => state.profiles[index]
}

const actions = {
  addProfile: (store, profileID) => {
    Vue.http.get(apiRoot + 'profile/get/' + profileID).then((response) => {
      console.log('SUCCESS: profile addition', response)
      store.commit('ADD_PROFILE', response.data.data)
    }, (response) => {
      console.error('ERR: profile addition request', response)
      switch (response.status) {
        case 400:
          console.log('Bad request, bad ID, bad idea')
          break
        case 404:
          console.log('Le profil n\'existe pas')
          break
        default:
          console.log('Unknown error')
      }
    })

  },
  deleteProfil: (store, profile) => {
    Vue.http.get(apiRoot + 'profile/delete/' + profile.profileID).then((response) => {
      console.log('SUCCESS: profile deletion', response)
      store.commit('DELETE_PROFILE', profile)
    }, (response) => {
      console.error('ERR: profile deletion request', response)
      switch (response.status) {
        case 400:
          console.log('Bad request, bad ID, bad idea...')
          break
        case 401:
          console.log('Can\'t touch this!')
          break
        case 404:
          console.log('It\'s the profile that you search.')
          break
        default:
          console.log('Unknown error')
      }
    })
  },

  selectProfile: (store, id) => {
    Vue.http.get(apiRoot + 'profile/setCurrent/' + id).then((response) => {
      store.commit('SET_CURRENT_PROFILE', id)
    }, (response) => {
      console.error('ERR: selection profile', response)
    })
  },
  initProfiles: (store) => {
    Vue.http.post(apiRoot + 'user/profiles').then((response) => {
      response.data.data.profiles.forEach(profile => {
        if (profile !== null)
          store.commit('ADD_PROFILE', profile)
      })
    }, (response) => {
      console.log('ERR: récupération des profils', response)
    })
  },
  initUser: (store) => {
    Vue.http.post(apiRoot + 'user/get').then((response) => {
      store.commit('SET_USER', response.data.data)
    }, (response) => {
      console.error('Can\'t get user info', response)
    })
  },
  clearUser: store => store.commit('SET_USER', {userName: false})
}

export default new Vuex.Store({
  state: state,
  mutations: mutations,
  getters: getters,
  actions: actions,
  strict: true

})
