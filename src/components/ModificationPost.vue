<template>
	<md-layout>
		<md-layout class="ModificationPost" md-flex="25">
			<img :src="imageLink" alt="Photo post">
			<span>Choisir un filtre : </span>
			<md-layout  md-align="start">
				<md-input-container class="choiceFilter">
					<label>Filtre</label>
					<md-select name="filtre" v-model="filter.currentFiltre">
						<md-option :value="none">Pas de filtre</md-option>
						<md-option v-for="filtre in filter.filtres" :value="filtre">{{filtre}}</md-option>
					</md-select>
					<md-button @click.native="applyFilter	">Appliquer<md-button>
				</md-input-container>
				<img :src="this.post.contactPicture" alt="filtresImage">
			</md-layout>

			<md-input-container>
				<label>Description</label>
				<md-textarea v-model="this.post.post_description"></md-textarea>
			</md-input-container>
			
			<md-chips v-model="tags" md-input-placeholder="ajouter un tag"	@change="addTag"  :md-max="15">
			  		<template scope="chip" @delete="deleteTag(chip.value)">{{ chip.value }}</template>	
			</md-chips>
			
	 		<md-layout md-flex="100"><md-layout  md-align="end"><md-switch v-model="comment"  name="allowComment" class="md-primary">Autoriser les commentaires</md-switch></md-layout></md-layout>
			<md-layout  md-align="end"><md-button @click.native="saveChanges" class="md-raised md-primary">Enregistrer</md-button></md-layout>

		</md-layout>
	</md-layout>	
</template>



<script>
import apiRoot from './../config.js'
import formVerifications from './../formVerifications.js'

export default{
	mixins: [formVerifications],
	
	name: "ModificationPost",
	props: ['post'],
	data(){
		return{
			
			comment : true
			
		}
	},
	computed: {
		filter(){
			return{
				filtres : ['amaro', 'brannan', 'clarendon', 'earlybird', 'hefe', 'hudson', 'inkwell', 'kelvin', 'lark', 'lofi', 'mayfair', 'moon', 'nashville', 'reyes', 'rise', 'sierra', 'sutro', 'toaster', 'valencia', 'walden', 'willow', 'xproii'],
				currentFiltre : 'none'
			}
		},
		tags(){
			return ['chevals', 'ornithorynque']
		},
		allowComment(){
			if(this.comment) return 1
			return 0
		},
		imageLink(){
			
			if (this.post.editedPicture != null)	return this.post.editedPicture
			else return this.post.originalPicture
			

		}
	},
	methods: {
		applyFilter(){			
			this.$http.get(apiRoot + 'post/setfilter/' + this.post.post_id + '/' + this.currentFiltre).then((response)=>{
				if(currentFiltre== 'none'){ this.imageLink = response.data.data.originalPicture}			
				else{ this.imageLink = response.data.data.editedPicture}
				},(response)=>{
					switch (response.status) {
						case 400 :
							console.log('Impossible de publier le post')
							break
						case 401 :
							console.log('L\'utilisateur n\'est pas connecté/ne possède pas ce post/ne peut pas le modifier')
							break
				}
			})
		},
		deleteTag(tag){
			this.$http.get(apiRoot + 'tag/delete/' + this.post.post_id + '/' + tag).then((response)=>{
				
			},(response)=>{
				switch (response.status) {
					case 401 :
						console.log('Pas l\'autorisation de modifier le post : ID du profil différent de l\'ID profile du post.')
						break
					case 404 :
						console.log('Le tag pour ce post n\'existe pas.') 
						break
				}
			})
		},
		addTag(tagArray){

			var len = tagArray.length
			var tag = tagArray[len-1]
			if(this.verif_tag(tag)){
				this.$http.get(apiRoot + 'tag/add/' + this.post.post_id + '/' + tag).then((response)=>{
					
				},(response)=>{
					switch (response.status) {
						case 401 :
							console.log('Pas l\'autorisation de modifier le post : ID du profil différent de l\'ID profile du post.')
							break
						case 404 :
							console.log('Le tag pour ce post existe déjà.') 
							break
					}
				})
			}
			else this.tags.pop()

		},
		saveChanges(){
			this.$http.post(apiRoot + 'post/update/DESCRIPTION/' + this.post.post_id,{
				desc : post.post_description
			}).then((response)=>{				
				this.$http.post(apiRoot + 'post/update/ALLOWCOMMENTS/' + this.post.post_id,{
					 allowComments : this.allowComment
				}).then((response)=>{
					this.$emit('close', 'dialog4')
				},(response)=>{
					switch (response.status) {
						case 400 :						

							console.log('La variable GET' + this.post.post_id + 'n\'est pas un ID OU une variable POST est absente')
							break
						case 405 : 
							console.log('Le field spécifié n\'est pas supporté.')
							break
					}
				})	
			},(response)=>{
				switch (response.status) {
					case 400 :
						console.log('La variable GET' + this.post.post_id + 'n\'est pas un ID OU une variable POST est absente')
						break
					case 405 : 
						console.log('Le field spécifié n\'est pas supporté.')
						break
				}
			})
		}


	}
}

</script>

<style>
.ModificationPost{
	padding: 10px;
}
img{
	width: 100%;
}
</style>