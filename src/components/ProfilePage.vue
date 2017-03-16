<template>
	<md-layout md-gutter>
		<!-- infos du profil actif -->
		<infosEditable v-if="current" :currentProfileProp="currentP" :nmbPosts="nmbPosts" :listFollowers="listFollowers" :listFollowings="listFollowings"></infosEditable>
		<!-- infos du profil qu'on visite -->
		<informationsProfilAutre v-else-if="!current" :currentProfileProp="currentP" :nmbPosts="nmbPosts" :listFollowers="listFollowers" :listFollowings="listFollowings"></informationsProfilAutre>

		<!-- posts du profil actif -->
		<previewsPostsPerso v-if="current" :currentProfileProp="currentP"></previewsPostsPerso>
		<!-- posts du profil qu'on visite -->
		<previewsPosts v-else-if="!current" :currentProfileProp="currentP" :profile="profile"></previewsPosts>
		
	</md-layout>	
</template>

<script type="text/javascript">
// components
import infosEditable from './infosProfileEditable.vue'
import informationsProfilAutre from './Informations-profil.vue'
import previewsPosts from './previewsPosts.vue'
import previewsPostsPerso from './previewsPostsPerso.vue'
// imports autres
import Vuex from 'vuex'
import store from './connectionStore.js'
import apiRoot from './../config.js'

export default{
	name: 'pageProfile',
	store: store,
	components: {
		infosEditable,
		informationsProfilAutre,
		previewsPosts,
		previewsPostsPerso
	},
	data () {
		return {
			current: false,
			nmbPosts: 0,
			ListFollowers: null,
			listFollowings: null,
			profile: null,
			views: null
		}
	},
	props: {
		ID: Number
	},
	computed: {
		// recuperation des informations sur le profil courant
		...Vuex.mapGetters({
	      	currentP: 'currentProfile'
		})
	},
	methods: {

		// test si le profil visite est le meme que le profil courant
		activeProfile () {
			console.log(this.currentP)
			if (this.currentP.profileID === this.ID) { this.current = true }
	    },

		// Recuperation du profil de la page
		getProfile () {
			console.log(this.ID)
			this.$http.get(apiRoot + 'profile/get/' + this.ID).then( response => {
						console.log('SUCESS getProfile : ', response)
						//console.log(response)
					    this.profile = response.data.data	
				}, response => {
					console.error('ERROR getProfile : ', response)
					switch (response.status) {
						case 200:
							console.log("OK Data")
							break
						case 400:
							console.log('La variable GET ' + this.ID + ' n\est pas un ID')
							break
						case 404:
							console.log('Le profil spécifié n\'existe pas')
							break
						default:
							console.log('Unknown error')
					}
				})
		},

		// Recuperation du nombre de posts
		getNmbPosts () {
			this.$http.get(apiRoot + 'profile/nbrposts/' + this.ID).then((response) => {
					console.log('SUCESS getNmbPosts : ', response)
					this.nmbPosts = response.data.data.nbrPosts
				 	/* profileID : ID du profil, 
    				nbrPosts : Nombre de posts  */
				}, (response) => {
					console.error('ERROR getNmbPosts : ', response)
					switch (response.status) {
						case 200:
							console.log("OK Data")
							break
						case 400:
							console.log('La variable GET ' + this.ID + ' n\est pas un ID')
							break
						case 401:
							console.log('Le profil courant n\'est pas autorisé a voir les posts de ce profil')
							break
						case 404:
							console.log('Le profil spécifié n\'existe pas')
							break
						default:
							console.log('Unknown error')
					}
				})
		},

		// recuperation de la iste des abonnes
		getListFollowers () {
			this.$http.get(apiRoot + 'profile/followers/' + this.ID).then((response) => {
						console.log('SUCESS getListFollowers : ', response)
						this.ListFollowers = response.data.data
				}, (response) => {
					console.log('ERROR getListFollowers : ', response)
					switch (response.status) {
						case 200:
							console.log("OK Data")
							break
						case 401:
							console.log(' Vous n\'avez pas le droit de voir cette liste')
							break
						default:
							console.log('Unknown error')
					}
				})
		},

		// recuperation de la liste des abonnements
		getListFollowings () {
			this.$http.get(apiRoot + 'profile/followings/' + this.ID).then((response) => {
						console.log('SUCESS getListFollowings : ', response)
						this.ListFollowings = response.data.data
				}, (response) => {
					console.log('ERROR getListFollowings : ', response)
					switch (response.status) {
						case 200:
							console.log("OK Data")
							break
						case 401:
							console.log(' Vous n\'avez pas le droit de voir cette liste')
							break
						default:
							console.log('Unknown error')
					}
				})
		},

		// Ajouter une vue au profil
		addView () {
			this.$http.get(apiRoot + 'profile/addView/' + this.ID).then((response) => {
					console.log('SUCCESS addView : ', response)
					this.views = response.data.data
				}, (response) => {
					console.log('ERROR getListFollowings : ', response)
					switch (response.status) {
						case 200:
							console.log("OK Data")
							break
						case 400:
							console.log('La variable GET profileID n\'est pas un ID OU la variable FILE profilePicture est absente')
							break
						case 404:
							console.log('Le profil spécifié n\'existe pas')
						default:
							console.log('Unknown error')
					}
				})
		}
	},
	mounted () {
		console.log(this.currentP)
		this.getProfile ()
		this.getNmbPosts ()
		this.getListFollowers ()
		this.getListFollowings ()
		this.activeProfile ()
		this.addView()
		// Requete ajout d'une vue
	}
}
	
</script>

<style type="text/css" scoped>

	
</style>