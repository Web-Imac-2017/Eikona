import Vue from 'vue'
import Vuex from 'vuex'
import apiRoot from './../config.js'

Vue.use(Vuex)


const state = {
  user: {
    userName: false
  },
  userProfiles: [],
  currentProfile: -1
}

const mutations = {
  SET_USER: (state, newUser) => state.user = newUser,
  ADD_PROFILE: (state, profile) => state.userProfiles.push(profile),
  DELETE_PROFILE: (state, profile) => state.userProfiles.filter(i => i !== profile),
  ADD_PROFILES: (state, profileArray) => state.userProfiles = profileArray,
  DELETE_ALL_PROFILE: state => {
    state.userProfiles = []
    state.currentProfile = -1
  },
  SET_CURRENT_PROFILE: (state, profileid) =>{ 
    state.currentProfile = state.userProfiles.findIndex(el => el.profileID == profileid)
  }
}

const getters = {
  getUser (state) { return state.user },
  profiles (state) { return state.userProfiles },
  currentProfile (state) {
    if (state.currentProfile >= 0) return state.userProfiles[state.currentProfile]
    else return false
  },
  currentProfileIndex (state) { return state.currentProfile },
  getProfile (state, index) { return state.userProfiles[index] }
}

const actions = {
  updateUser (store, user) {
    store.commit('SET_USER', user)
  },
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
  selectProfile: (store, profileId) => {
    Vue.http.get(apiRoot + 'profile/setCurrent/' + profileId).then((response) => {
      store.commit('SET_CURRENT_PROFILE', profileId)
    }, (response) => {
      console.error('ERR: selection profile', response)
    })
  },
  initProfiles: (store) => {
    Vue.http.post(apiRoot + 'user/profiles/').then((response) => {
      store.commit('DELETE_ALL_PROFILE')
      console.log('Récupération profiles : ', response)
      if (response.data.data.profiles.length > 0)
        store.commit('ADD_PROFILES', response.data.data.profiles)
    }, (response) => {
      console.log('ERR: récupération des profils', response)
    })
  },
  clearProfiles: (store) => store.commit('DELETE_ALL_PROFILE'),
  initUser: (store) => {
    Vue.http.post(apiRoot + 'user/get').then((response) => {
      console.log('Récupération utilisateur : ', response)
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
