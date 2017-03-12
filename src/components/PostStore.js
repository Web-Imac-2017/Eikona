import Vue from 'vue'
import Vuex from 'vuex'

import apiRoot from './../config.js'

Vue.use(Vuex);

const state = {
	feedEvents: [],
	feedLastEventTimestamp: false,

	popularPosts: []
}

const mutations = {
	ADD_POPULAR_POST: (state, post) => state.popularPosts.push(post),
	DELETE_POPULAR_POST: (state, post) => state.popularPosts.filter(i => i !== post),

	ADD_FEED_EVENT: (state, post) => state.feedEvents.push(post),
	DELETE_FEED_EVENT: (state, post) => state.feedEvents.filter(i => i !== post),

	SET_LAST_EVENT_TIMESTAMP: (state, timestamp) => state.feedLastEventTimestamp = timestamp
}

const getters = {
	feedEvents: state => state.feedEvents,
	feedPosts: state => state.feedEvents.filter(e => e.type === 'post'),
	feedComments: state => state.feedEvents.filter(e => e.type === 'comment'),
	feedLikes: state => state.feedEvents.filter(e => e.type === 'like'),
	feedFollows: state => state.feedEvents.filter(e => e.type === 'follow'),
	feedLastEventTimestamp: state => state.feedLastEventTimestamp,
	popularPosts: state => state.popularPosts
}

const actions = {
	nextPopularPosts (store, number, exclude) {
		Vue.http.post(apiRoot + 'post/popular/' + number, {
			exclude: exclude
		}).then(response => {
			console.log('popular feed request : ', response)
			response.data.data.posts.forEach(item => store.commit('ADD_POPULAR_POST', item))
		}, response => {
			console.error('ERR: chargement posts populaires')
		})
	},
	nextFeedEvents (store, number) {
		var before = '/' + (getters.feedLastEventTimestamp!==false?getters.feedLastEventTimestamp:'')
		Vue.http.get(apiRoot + 'profile/feed/' + number + before).then(
			(response) => {
				console.log('Feed resp : ', response)
				response.data.data.feed.forEach(e => store.commit('ADD_FEED_EVENT', e))
				if (response.data.data.feed.length > 0)
					store.commit('SET_LAST_EVENT_TIMESTAMP', response.data.data.feed[response.data.data.feed.length - 1].time)
			},
			(response) => {
				console.error('ERR: load feed events', response)
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
