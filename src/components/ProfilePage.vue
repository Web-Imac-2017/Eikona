<template>
	<md-layout md-gutter>
		<infosEditable></infosEditable>
		<previewsPosts></previewsPosts>
	</md-layout>	
</template>

<script type="text/javascript">
	import addPost from './addPost.vue'
	import infosEditable from './infosProfileEditable.vue'
	import informationsProfilAutre from './Informations-profil.vue'
	import previewsPosts from './previewsPosts.vue'

	export default{
		name: 'pageProfile',
		components: {
			addPost,
			infosEditable,
			informationsProfilAutre,
			previewsPosts
		}
	}
	
</script>

<style type="text/css" scoped>
	
</style>