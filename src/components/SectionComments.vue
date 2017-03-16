<template>
	<md-layout id="sectionComments" md-column>
		
		<md-list >
						
			<commentaire v-for="(comment,index) in comments" :content="comment.comment_text" :nbrLike="getLike(index)" :id="comment.comment_id" @incrementLike="addLikeComment" ></commentaire>
						
			
		</md-list>
		<md-input-container>
				<md-input type="text" class="new-comment" placeholder="Ajouter un commentaire" @keyup.enter="addComment" v-model="newComment"></md-input>
				<md-button @click.native="addComment">Commenter</md-button>
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
	props: ['comments', 'post', 'commentsLike'],
	data(){
		return{
			newComment:''
		}
	},

	methods:{
		getLike(index){
			return this.commentsLike[index]
		},
		addLikeComment(id){

				this.$http.get(apiRoot + 'comment/like/'+id).then((response)=> {

					var i
					var len
					for (i=0, len=this.comments.length; i < len; i++){
						if (this.comments[i].comment_id == id){
							this.commentsLike[i]++
							
						}

					}
				},(response)=>{
					console.log(response)
					switch (response.status) {
						case 404: 
							console.log('L\'id du commentaire ne renvoie à aucun commentaire')
							break
						case 401:
							console.log('Pas de profil courant sélectionné OU User pas connecté OU vous ne suivez pas la personne')
							break
						case 400:
							this.$http.get(apiRoot + 'comment/unlike/' +id).then((response)=>{
								var i
								var len
								for (i=0, len=this.comments.length; i < len; i++){
									if (this.comments[i].comment_id == id){
										console.log('like moins 1')
										this.commentsLike[i]--
									}
								}
							},(response)=>{	
								console.log('On ne peut pas aimer son propre commentaire')
								
							})
						break
					}

				}) 
				console.log(this.commentsLike)

			},

			addComment(){
				console.log('pouet')
				this.$http.post(apiRoot + 'comment/create/'+ this.post.postID, {
					commentText: this.newComment

				}).then((response)=>{
					this.comments.push({
						profile_id : response.data.data.profileID,
						comment_text : this.newComment,
						comment_id : response.data.data.commentID		
						
					})
					this.commentsLike.push(0)
					this.newComment=''

				},(response)=>{
					switch (response.status) {
						case 404:
							console.log('L\'id du post ne renvoie à aucun post')
							break
						case 400: 
							console.log('Au moins une des variables POST n\'a pas été transmise OU Les commentaires sont désactivés pour ce post')
							break
						case 401:
							console.log('Vous ne suivez pas la personne OU pas de profil courant sélectionné OU user pas connecté')
							break
					}

				})

			},
			delComment(id){
				this.$http.get(apiRoot + 'comment/delete/'+ id).then((response)=>{
					var i
					var len=this.comments.length
					for (i=0; i < len; i++){
						if (this.comments[i].comment_id == id){
							this.comments[i].comment_id=this.comments[len].comment_id
							this.comments[i].comment_texte=this.comments[len].comment_texte
							this.commentsLikes[i]=this.commentsLikes[len]
							this.comments.pop()	
							this.commentsLikes.pop()	
						}
					}						
					},(response)=>{
						switch(response.status){
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