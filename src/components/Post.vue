<template>
	<div id="post">
		<md-card class='post'>
			<md-card-header>
				<md-avatar>
		  			<img src="assets/Eiko.png" alt="Avatar">
				</md-avatar>
				<div id="infoPosteur">{{userName}}</div>

			</md-card-header>

	 		<md-card-media>
				<img :src=imageLink alt="Photo test">
	  		</md-card-media>
	  		<div class="md-title">{{title}}</div>
		  	<md-card-actions>
		  	<div v-if="error_message != ''" class="error-msg">{{ error_message }}</div>
		  	<md-button-toggle>
				<md-button class="md-icon-button" @click.native="addLike"><md-icon>favorite</md-icon> </md-button>
			</md-button-toggle>
				<span >{{nbrLike}}</span>
			</md-card-actions>
			<md-card-content>
				<div class="description">{{description}}</div>
				<input type="text" class="new-comment" placeholder="Ajouter un commentaire" @keyup.enter="addComment" v-model="newComment">
				<ul class="comments-list">
					<li class="comment" v-for="comment in comments">
						<commentaire :content="comment.message" :nbrLike="comment.nbrLikeComment" :id="comment.id" @incrementLike="addLikeComment"></commentaire>

					</li>
				</ul>
			</md-card-content>
		</md-card>

	</div>
</template>



<script>
	import store from './postStore'
	import commentaire from './Comment.vue'
	export default{
		name: 'post',
		components: {
			commentaire
		},
		data () {
			return {
				userName : "JackieDu29",
				imageLink: "assets/testPhoto.jpg",
				nbrLike : 0,
				nbrJour : 0,
				title: 'Look at my mustach',
				description: 'une description',
				comments: [{
					id: 1,
					message: "commentaire de test",
					nbrLike:0
				}],
				newComment: '',
				error_message: ''

			}
		},

		methods: {
			addLike () {

				this.$http.get('/Eikona/do/post/like/<postID>/').then((response) =>{
					this.nbrLike++
				},(response)=>{
					switch (response.code) {
						case 401:
							this.error_message = "Le post spécifié n'existe pas OU l'user n'a pas de profil courant OU vous ne suivez pas la personne"
							break
						case 400:
							this.$http.get('/Eikona/do/post/unlike/<postID>/').then((response)=>{
								this.nbrLike--
							},(response)=>{
								this.error_message = "On ne peut pas aimer son propre post"
							})


							break
						case 406:
							this.error_message= "Le profil courant a liké plus de 200 post durant les 60 dernières minutes. (Securité Anti-Bot)"
							break

					}

				})


			},
			addLikeComment(commentID){
				this.$http.get('/Eikona/do/comment/like/'+ commentID).then((response)=> {
					this.comments.filter(comment => comment.id === commentID)
				},(response)=>{
					switch (response.code) {
						case 404:
							this.error_message = " L'id du commentaire ne renvoie à aucun commentaire"
							break
						case 401:
							this.error_message = "Pas de profil courant sélectionné OU User pas connecté OU vous ne suivez pas la personne"
							break
						case 400:
							this.$http.get('/Eikona/do/post/unlike/'+ commentID).then((response)=>{
								this.comments[i].nbrLike--
							},(response)=>{
								this.error_message = "On ne peut pas aimer son propre commentaire"
							})
						break
					}

				})

			},

			addComment(){
				this.$http.post('/Eikona/do/comment/create/<postID>/', {
					commentText: this.newComment
				}).then((response)=>{
					this.comments.push({
						message: this.newComment,
						nbrLikeComment: 0
					})
					this.newComment=""

				},(response)=>{
						this.error_message=''
				})
			}
		}
	}
</script>

<style scoped>
	li{
		text-decoration: none,

	}
	.post{
		width: 500px;
	}
</style>
