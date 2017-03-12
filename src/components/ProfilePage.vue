<template>
	<md-layout md-gutter>
		<infosEditable v-if="current" :profile="visitedProfile" :currentP="currentProfile"></infosEditable>
		<informationsProfilAutre v-else-if="!current" :profile="visitedProfile"></informationsProfilAutre>

		<previewsPosts v-if="!current"></previewsPosts>
		<previewsPostsPerso v-else-if="current" :profile="profile"></previewsPostsPerso>
	</md-layout>	
</template>

<script type="text/javascript">
import infosEditable from './infosProfileEditable.vue'
import informationsProfilAutre from './Informations-profil.vue'
import previewsPosts from './previewsPosts.vue'
import previewsPostsPerso from './previewsPostsPerso.vue'
import Vuex from 'vuex'
import store from './connectionStore.js'

export default{
	name: 'pageProfile',
	components: {
		infosEditable,
		informationsProfilAutre,
		previewsPosts,
		previewsPostsPerso
	},
	data () {
		return {
			current: false,
			visitedProfile
		}
	},
	computed: {	
		...Vuex.mapGetters([
	      	'currentProfile'
	   	]),
	    activeProfile () {
	    	if(this.currentProfile.profileID === this.$route.params.profileID) {this.current = true;}
	    }
	},
	created () {
		this.$http.get(apiRoot + '/profile/get/' + this.$route.params.profileID).then((response) => {
					{
					    profil.id: //profileID,
					    ownerID: //ID du user propriétaire du profil,
					    profileName: //Nom du profil,
					    profileDesc: //Description du profil,
					    profileCreateTime: //Timestamp de la création du profil,
					    profileViews: //Nombre de vues du profil,
					    profileIsPrivate: //Confidentialité du profil
					}
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.$route.params.profileID + ' n\est pas un ID')
							break
						case 404:
							console.log('Le profil spécifié n\'existe pas')
							break
						default
					}
				})
	},
	mounted () {

		console.log(this.$route.params.profileID);
	}
}
	
</script>

<style type="text/css" scoped>

	
</style>