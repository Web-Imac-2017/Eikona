<template>
	<md-layout md-gutter>
		<infosEditable v-if="currentProfile"></infosEditable>
		<informationsProfilAutre v-else-if="!currentUser"></informationsProfilAutre>

		<previewsPosts v-if="!currentProfile"></previewsPosts>
		<previewsPostsPerso v-else-if="currentProfile"></previewsPostsPerso>


	</md-layout>	
</template>

<script type="text/javascript">
import infosEditable from './infosProfileEditable.vue'
import informationsProfilAutre from './Informations-profil.vue'
import previewsPosts from './previewsPosts.vue'
import previewsPostsPerso from './previewsPostsPerso.vue'

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
			currentProfile: false
		}
	}
}
	
</script>

<style type="text/css" scoped>

	
</style>