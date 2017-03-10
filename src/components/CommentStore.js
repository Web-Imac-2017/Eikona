import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
	 state: {
	 	message: '',
	 	nbrLike: ''
	 }
	 mutations: {
	 	SET_MESSAGE(state, edit) => {state.message = edit},
	 	ADD_LIKE(state) => {state.nbrLike ++},
	 	REMOVE_LIKE(state) => {state.nbrLike --}
	 },
	 getters: {
	 	nbrLike: state => {return state.nbrLike},
	 	message: state => {return state.message}
	 }


})