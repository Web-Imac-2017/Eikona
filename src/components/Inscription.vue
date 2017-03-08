<template>
	<div id="inscription">
		<h2>Pas encore inscrit(e) ?</h2>
		<md-icon>keyboard_arrow_down</md-icon>
		<inscriptionForm></inscriptionForm>
	</div>
</template>


<script>
import inscriptionForm from './InscriptionForm.vue'
export default {
	name: 'inscription',
	components: {
		inscriptionForm
	}
}
</script>

<style scoped>

</style>
