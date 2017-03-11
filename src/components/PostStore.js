import Vue from 'vue'
import Vuex from 'vuex'

import apiRoot from './../config.js'

Vue.use(Vuex);

const maxPosts = 10

// 3 days in millisecondes
const timestampStep = 259200000

const state = {
	followings: [],
	subscriptionsPosts: [],
	subsPostOffset: 0,
	timestampOffset: 0,

	popularPosts: []
}

const mutations = {
	ADD_FOLLOWING: (state, profileId) => state.followings.push(profileId),
	DELETE_FOLLOWING: (state, profileId) => state.followings.filter(i => i.profile_id !== profileId),
	ADD_POST_SUBCRIPTION: (state, post) => state.subscriptionsPosts.push(post),
	DELETE_POST_SUBSCRIPTION: (state, post) => state.subscriptionsPosts.filter(i => i !== post),
	LOADED_POST: (state, increment) => state.subsPostOffset += increment,

	ADD_POPULAR_POST: (state, post) => state.popularPosts.push(post),
	DELETE_POPULAR_POST: (state, post) => state.popularPosts.filter(i => i !== post)
}

const getters = {
	followings: state => state.followings,
	subscriptionsPosts: state => state.subscriptionsPosts,
	subsPostsLoaded: state => state.subscriptionsPosts.length,
	popularPosts: state => state.popularPosts
}

const actions = {
	nextPopularPosts (store, number) {
		var exclude = ''
		getters.popularPosts.forEach(i => exclude += (i.postID + ','))
		Vue.http.post(apiRoot + 'post/popular/' + number, {
			exclude: exclude
		}).then(response => {
			response.data.data.posts.forEach(item => {
				store.commit('ADD_POPULAR_POST', item)
			})
		}, response => {
			console.error('ERR: chargement posts populaires')
		})
	},
	initSubscriptions (store, profileId) {
		Vue.http.get(apiRoot + 'profile/followings/' + profileId).then(
			(response) => {
				response.data.data.followings.forEach(profile => store.commit('ADD_FOLLOWING', profile.profile_id))
			},
			(response) => {
				console.error('ERR: load subscriptions', response)
			})
	},
	subscribe (store, profileId, subscribe = 1) {
		Vue.http.get(apiRoot + 'follow/' + profileId + '/' + subscribe).then(
			(response) => {
				store.commit('ADD_FOLLOWING', profileId)
			},
			(response) => {
				console.error('ERR: Can\'t subscribe t this.')
			})
	},
	unsubscribe (store, profileId) {
		Vue.http.get().then(
			(response) => {
				store.commit('DELETE_FOLLOWING', profileId)
			},
			(response) => {
				console.error('ERR: Can\'t unsubscribe to this');
			})
	}
}

export default new Vuex.Store({
	state: state,
	mutations: mutations,
	getters: getters,
	actions: actions,
	strict: true
})
