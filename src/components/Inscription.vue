<template>
	<div id="inscription">
		<p>Pas encore inscrit(e) ?</p>
		<inscriptionForm></inscriptionForm>
	</div>
</template>


<script>
import inscriptionForm from './InscriptionForm.vue'


export default {
	name:'inscription',
	components:{
		inscriptionForm
	}
}
</script>

<style scoped>

</style>