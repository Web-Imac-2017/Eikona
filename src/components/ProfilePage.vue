<template>
	<md-layout md-gutter>
		<md-layout v-if="current">
			<infosEditable :profile="profile"</infosEditable>
			<previewsPostsPerso :profile="profile"></previewsPostsPerso>
		</md-layout>
		<md-layout v-else>
			<informationsProfilAutre :profile="profile"></informationsProfilAutre>
			<previewsPosts :profile="profile"></previewsPosts>
		</md-layout>
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
			profile: null,
			followers: [],
			followings: []
		}
	},
	props: {
		ID: Number,
		current: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		// recuperation des informations sur le profil courant
		...Vuex.mapGetters([
    	'currentProfile'
   	])
	},
	mounted () {
		if (this.current) {
			this.profile = this.currentProfile
			this.getNmbPosts (this.profile.profileID)
			this.getListFollowers (this.profile.profileID)
			this.getListFollowings (this.profile.profileID)
		} else {
			this.getProfile (this.ID)
		}
	},
	methods: {
		// Recuperation du profil de la page
		getProfile (id) {
			console.log('Profile page id prop : ' + id)
			this.$http.get(apiRoot + 'profile/get/' + id).then( response => {
				console.log('SUCESS getProfile : ', response)
		    this.profile = response.data.data

				this.getNmbPosts (id)
				this.getListFollowers (id)
				this.getListFollowings (id)
				// Requete ajout d'une vue
				this.addView(id)

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
		getNmbPosts (id) {
			this.$http.get(apiRoot + 'profile/nbrposts/' + id).then((response) => {
					console.log('SUCESS getNmbPosts : ', response)
					this.profile = {...this.profile,
						nbPosts: response.data.data.nbrPosts
					}
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
		getListFollowers (id) {
			this.$http.get(apiRoot + 'profile/followers/' + id).then((response) => {
						console.log('SUCESS getListFollowers : ', response)
						this.followers = response.data.data
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
		getListFollowings (id) {
			this.$http.get(apiRoot + 'profile/followings/' + id).then((response) => {
						console.log('SUCESS getListFollowings : ', response)
						this.followings = response.data.data
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
		addView (id) {
			this.$http.get(apiRoot + 'profile/addView/' + id).then((response) => {
					console.log('SUCCESS addView : ', response)
					this.profile.profileViews = response.data.data
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
	}
}

</script>

<style type="text/css" scoped>


</style>
