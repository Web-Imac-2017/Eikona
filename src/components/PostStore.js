import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
	state:{
		username: '',
		nbrLike: 0,
		nbrJours: 0,
		title: '',
		comments: [],
	},
	mutations: {
		SET_TITLE (state, titre) => {state.title=titre},
		SET_DESCRIPTION (state, describe) => {state.description=describe}
	},
	getters: {
		postID: state => {return state.postID},
		nbrLike: state => {return state.nbrLike}
	}



})