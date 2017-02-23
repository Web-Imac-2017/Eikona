<template>
	<div id="inscription">
		<p>Pas encore inscrit(e) ?</p>
		<inscriptionForm></inscriptionForm>
	</div>
</template>


<script>
let inscriptionForm = require('./InscriptionForm.vue')


export default {
	name:'inscription',
	components:{
		inscriptionForm
	}
}
</script>

<style scoped>

</style>