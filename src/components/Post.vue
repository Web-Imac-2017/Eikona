<template>
	<md-layout>

		<md-card class='post'>
			<md-card-header>
				<md-layout>
					<md-layout md-align="start" class="avatar_poster">

					<md-list>
						<md-list-item>
							<md-avatar>
						  		<img :src="profile.profile_picture" alt="Avatar">		  			
							</md-avatar>
							<span>{{profile.profile_name}}</span>
						</md-list-item>

					</md-layout>
					<md-list>

					<md-layout md-align="end">
						<PostSettings class="md-list-action" :posteurID="1"	></PostSettings>
					</md-layout>
				</md-layout>
			</md-card-header>

	 		<md-card-media>
				<img :src="imageLink" alt="Photo post">
  		</md-card-media>


	  		<md-layout id="infosPost" >	  			
	  			<md-layout md-flex="80"><div class="description">{{post.post_description}}</div></md-layout>
				<md-layout md-align="end" ><md-button id='post-Like' class="md-icon-button" @click.native="addLike('post-Like')"><md-icon>favorite</md-icon> </md-button>
				<span>{{like}}</span> </md-layout> 		
		  						
			</md-layout> 

	  		
	  				

			<md-layout id="post-tagContainer">
				<md-chip v-for="tag in tags" class="tag" disabled>{{tag}}</md-chip>	
			</md-layout> 
	
		  	


			<md-card-content>

				<p>{{comments.length}} commentaires : </p>
				<md-button class="md-icon-button" id="display-more-comments"><md-icon>expand_more</md-icon></md-button>
				<sectionComments :comments="comments" :errorMessage="errorMessage" :postID="post.post_id"></sectionComments>
			</md-card-content>

		</md-card>
	</md-layout>
</template>



<script>

	import store from './postStore.js'
	import apiRoot from './../config.js'
	import SectionComments from './SectionComments.vue'
	import PostSettings from './PostSettings.vue'


	export default{
		name: 'postFront',
		components: {
			SectionComments,
			PostSettings
		},
		data () {

			return {			
				comments: [],
				imageLink: 'assets/testPhoto.jpg'

			}
		},
		props: ['post'],
		computed: {
			profile () {
				// Recuperer le profile du posteur
				return {
					profile_name: 'JackieDu29',
					profile_picture: 'assets/Eiko.png'
				}
			},
			
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
					}
			},
			tags () {
				// Récpérer les tags attachés à ce post
				this.$http.get(apiRoot + 'post/tags/' + this.post.postID).then((response)=>{
					this.tags = response.data.data.tags
					},(response)=>{			
					
					console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas le profil')
				})
			}
		},
		methods: {
			getComments () {
				// Recuperer les commentaires du post
				this.$http.get(apiRoot + 'post/comments/' + this.post.postID).then((response)=>{
					this.comments = response.data.data.comments
					},(response)=>{			
					
					console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas le profil')
				})
						
			},
			addLike (id) {
				this.$http.get(apiRoot + 'post/like/'+this.post.post_id).then((response) => {
					this.nbrLike++
					document.getElementById(id).classList.add('md-primary')
				},(response)=>{
					switch (response.status) {
						case 401:
							console.log('Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas la personne')
							break
						case 400:
							this.$http.get(apiRoot + 'post/unlike/'+this.post.post_id).then((response)=>{
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

	#display-more-comments{
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
