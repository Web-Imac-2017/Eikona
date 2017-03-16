<template>
	<md-layout>
		<md-card class='post'>
			<md-card-header>
				<md-layout>
					<md-layout md-align="start" class="avatar_poster">
						<md-list>
							<md-list-item>
								<md-avatar>
							  		<img :src="profilePost.profilePict" alt="Avatar">
								</md-avatar>
								<span>{{profilePost.profileName}}</span>
							</md-list-item>
						</md-list>
					</md-layout>
					<md-layout md-align="end">
						<PostSettings class="md-list-action" :posteurID="profilePost.profileID"></PostSettings>
					</md-layout>
				</md-layout>
			</md-card-header>

	 		<md-card-media>
				<img :src="post.originalPicture" alt="Photo test">
  		</md-card-media>

			<md-layout class="infosPost">
  			<md-layout md-flex="80"><div class="description">{{post.desc}}</div></md-layout>
				<md-layout md-align="end" >
					<md-button id='post-Like' class="md-icon-button" @click.native="addLike('post-Like')"><md-icon>favorite</md-icon> </md-button>
					<span>{{like}}</span>
				</md-layout>
			</md-layout>

			<md-layout id="post-tagContainer">
				<md-chip v-for="tag in tags" class="tag" disabled>{{tag}}</md-chip>
			</md-layout>

			<md-card-content v-if="post.allowComments == 1">
				<p>{{comments.length}} commentaires : </p>
				<md-button class="md-icon-button display-more-comments" @click.native="showComments">
					<md-icon v-if="displayComs">expand_less</md-icon>
					<md-icon v-else>expand_more</md-icon>
				</md-button>
				<sectionComments v-show="displayComs" :comments="comments" :post="post" :commentsLike="commentsLike" :errorMessage="errorMessage" :post="this.post"></sectionComments>
			</md-card-content>
			<md-card-content v-else>
				<p>Commentaires desactivés</p>
			</md-card-content>
		</md-card>
	</md-layout>
</template>



<script>

	import store from './postStore.js'
	import apiRoot from './../config.js'
	import sectionComments from './SectionComments.vue'
	import PostSettings from './PostSettings.vue'


	export default{
		name: 'postFront',
		components: {
			sectionComments,
			PostSettings
		},
		data () {

			return {			
				comments: [],
				displayComs: false,
				commentsLike: [],
				like:0,
				tags:[]

			}
		},
		props: ['post', 'profilePost'],
		watch : {
			displayComs: 'getComments'
		},
		computed: {
			

			like () {
				// Récupérer le nombre de like du post

				this.$http.get(apiRoot + 'post/likes/' + this.post.postID).then((response)=>{
					this.like = response.data.data.nbOfLikes
				},(response)=>{
					switch (response.status) {
						case 401 :
							console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas le profil')
							break
						case 400 :
							console.log('Le post n\'a pas été aimé')
							this.like = 0
							break
					}
				})
			},
			tags () {
				// Récupérer les tags attachés à ce post

				this.$http.get(apiRoot + 'post/tags/' + this.post.postID).then((response)=>{
					this.tags = response.data.data.tags
					},(response)=>{			
					
					console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas le profil')
				})
			}


		},
		methods: {
			getComments () {
				if (!this.displayComs){
					this.comments = []
					this.commentsLike = []
					return
				}
				// Recuperer les commentaires du post
				this.$http.get(apiRoot + 'post/comments/' + this.post.postID).then((response)=>{
					this.comments = response.data.data.comments
					comments.forEach(comment => {
						this.$http.get(apiRoot + 'comment/likes' + comment.comment_id).then((response)=>{
							this.commentsLike.push(response.data.data.nbOfLikes)
						},(response)=>{
							switch (response.status) {
								case 404 :
									console.log('L\'id du commentaire ne renvoie à aucun commentaire')
									break
								case 401 :
									console.log('Vous ne suivez pas la personne')
									break
							}
						})
					})
					
					},(response)=>{			
					
					console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas le profil')
				})
						
			},
		
			showComments () {
				this.displayComs = !this.displayComs
				if (this.displayComs) {

				}

			},
			addLike (id) {
				this.$http.get(apiRoot + 'post/like/'+this.post.postID).then((response) => {
					this.nbrLike++
					document.getElementById(id).classList.add('md-primary')
				},(response)=>{
					switch (response.status) {
						case 401:
							console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas la personne')
							break
						case 400:
							this.$http.get(apiRoot + 'post/unlike/'+this.post.postID).then((response)=>{
								this.nbrLike--
								document.getElementById(id).classList.remove('md-primary')
							},(response)=>{
								console.log('On ne peut pas aimer son propre post')
							})
							break
						case 406:
							console.log('Le profil courant a liké plus de 200 post durant les 60 dernières minutes. (Securité Anti-Bot)')
							break
					}
				})
			}
		}
	}
</script>

<style scoped>

	.tag{
		margin: 0 4px 5px 0;
	}
	#post-tagContainer {
		padding: 0 10px;
		margin-bottom: 10px;
		margin-top: 10px
	}
	.description{
		font-size: 16px;
		margin: 10px 0 5px 10px;
	}

	.display-more-comments{
		left: 45%;
	}
	.avatar_poster{
		text-align: left;
	}
	.infosPost{
		display: inline-flex;
	}
	.infosPost span{
		margin-top: 13px;
		margin-right: 5px;
		font-size: 20px;
	}
	li{
		text-decoration: none;
	}
	.post{
		width: 500px;
	}
</style>
