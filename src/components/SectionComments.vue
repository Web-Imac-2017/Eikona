<template>
	<md-layout id="sectionComments" md-column>
		
		<md-list >
						
			<commentaire v-for="comment in comments" :content="comment.comment_texte" :nbrLike="commentsLike[index].nbOfLikes" :id="comment.comment_id" @incrementLike="addLikeComment" ></commentaire>
						
			
		</md-list>
		<md-input-container>
				<md-input type="text" class="new-comment" placeholder="Ajouter un commentaire" @keyup.enter="addComment" v-model="newComment"></md-input>
		</md-input-container>
	</md-layout>

</template>

<script>
import commentaire from './Comment.vue'
import apiRoot from './../config.js'

export default {
	name: 'sectionComments',
	components: {
		commentaire
	},
	props: [comments, errorMessage, post, commentsLike],
	data(){
		newComment:''
	}

	methods:{
		addLikeComment(id){
				this.$http.get(apiRoot + 'comment/like/'+id).then((response)=> {

					var i
					for (i=0, len=this.comments.length; i < len; i++){
						if (this.comments[i].comment_id == id){
							this.commentsLike[i]++
							document.getElementById(id).classList.add('md-primary')
						}

					}
				},(response)=>{
					switch (response.code) {
						case 404: 
							console.log(" L'id du commentaire ne renvoie à aucun commentaire")
							break
						case 401:
							this.errorMessage = "Pas de profil courant sélectionné OU User pas connecté OU vous ne suivez pas la personne"
							break
						case 400:
							this.$http.get('/Eikona/do/post/unlike/' +id).then((response)=>{
								commentsLike[i]--
							},(response)=>{
								this.errorMessage = "On ne peut pas aimer son propre commentaire"
							})
						break
					}

				}) 

			},

			addComment(){
				this.$http.post(apiRoot + 'comment/create/'+this.postID, {
					commentText: this.newComment

				}).then((response)=>{
					this.comments.push({
						 profile_id : response.data.data.profilID
						 comment_texte : this.newComment,
						
					})
					this.commentsLike.push(0)
					this.newComment=''

				},(response)=>{
					switch (response.code) {
						case 404:
							console.log("L'id du post ne renvoie à aucun post")
							break
						case 400: 
							this.errorMessage = "Au moins une des variables POST n'a pas été transmise OU Les commentaires sont désactivés pour ce post"
							break
						case 401:
							this.errorMessage ="Vous ne suivez pas la personne OU pas de profil courant sélectionné OU user pas connecté"
							break
					}

				})

			},
			delComment(id){
				this.$http.get('/Eikona/do/comment/delete/'+ id).then((response)=>{
					var i
					var len=this.comments.length
					for (i=0; i < len; i++){
						if (this.comments[i].comment_id == id){
							this.comments[i].comment_id=this.comments[len].comment_id
							this.comments[i].comment_texte=this.comments[len].comment_texte
							this.commentsLikes[i].nbOfLikes=this.commentsLikes[len].nbOfLikes
							this.comments.pop()	
							this.commentsLikes.pop()	
						}
					}						
					},(response)=>{
						switch(response.code){
							case 404:
								console.log("L'id du commentaire ne renvoie à aucun commentaire")
								break
							case 401:
								this.errorMessage = "Pas de profil courant sélectionné OU User pas connecté OU vous ne pouvez pas supprimé un commentaire qui n'est pas le vôtre."
								break
						}
					})

							
			}
	}
	
}
</script>

<style scoped>
	#sectionComments{
		display: block;
	}
</style>