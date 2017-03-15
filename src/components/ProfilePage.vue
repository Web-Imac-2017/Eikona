<template>
	<md-layout md-gutter>
		<infosEditable v-if="current"></infosEditable>
		<informationsProfilAutre v-else-if="!current"></informationsProfilAutre>

		<previewsPosts v-if="!current"></previewsPosts>
		<previewsPostsPerso v-else-if="current"></previewsPostsPerso>
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
			profile: this.$route.params
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

	},
	mounted () {

		console.log(this.$route.params.profileID);
	}
}
	
</script>

<style type="text/css" scoped>

	
</style>