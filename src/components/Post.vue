<template>
	<md-layout id="post">
		<div v-if="errorMessage != ''" class="error-msg">{{ errorMessage }}</div>
		<md-card class='post'>
			<md-card-header>
				<md-layout>
					
					<md-layout md-align="start" class="avatar_poster">
						<md-avatar>
					  		<img :src=avatarLink alt="Avatar">		  			
						</md-avatar>
						<span>{{userName}}</span>
					</md-layout>
					
					<md-layout md-align="end">
						<PostSettings class="md-list-action"></PostSettings>
					</md-layout>

				</md-layout>
				
			</md-card-header>

	 		<md-card-media>
				<img :src=imageLink alt="Photo test">
	  		</md-card-media>

	  		<md-layout id="infosPost" >	  			
	  			<md-layout md-align="start"><div class="md-title">{{title}}</div></md-layout>
				<md-layout md-align="end"><md-button id='post-Like' class="md-icon-button" @click.native="addLike('post-Like')"><md-icon>favorite</md-icon> </md-button>
				<span>{{nbrLike}}</span> </md-layout> 		
		  						
			</md-layout> 

	  		
	  		
	  		<md-chips v-model="tags" md-static>
 				 <template scope="chip">{{ chip.value }}</template>
			</md-chips>				

	
		  	

		  	

		  
			
			<md-card-content>
				<div class="description">{{description}}</div>
				<sectionComments :comments="comments" :errorMessage="errorMessage" :postID="postID"></sectionComments>
			</md-card-content>
			
		</md-card>
	
	</md-layout>
</template>



<script>
	

	//import store from './PostStore',
	import sectionComments from './SectionComments.vue'
	import PostSettings from './PostSettings.vue'

	export default{
		name : "PostFront",
		components: {sectionComments, PostSettings},

		data () {
			return {

				postID: 1,
				userName : 'JackieDu29',
				imageLink: 'assets/testPhoto.jpg',
				avatarLink: 'assets/Eiko.png',
				nbrLike : 0,
				title: 'Look at my mustach',
				description: 'une description',
				comments: [{
					id: 1,
					message: 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.',
					nbrLike:0
				},{
					id: 2,
					message: 'commentaire de test',
					nbrLike:0
				},{
					id: 3,
					message: 'commentaire de test',
					nbrLike:0
				},{
					id: 4,
					message: 'commentaire de test',
					nbrLike:0
				}],
				tags: ['chevals', 'ornithorynque'],
				newComment: '',
				errorMessage: ''

			}
		},

		methods: {
			addLike (id) {
				
				this.$http.get('/Eikona/do/post/like/'+this.postID).then((response) =>{
					this.nbrLike++
					document.getElementById(id).classList.add('md-primary')
				},(response)=>{
					switch (response.status) {
						case 401:
							this.errorMessage = 'Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas la personne'
							break
						case 400:
							this.$http.get('/Eikona/do/post/unlike/'+this.postID).then((response)=>{
								this.nbrLike--
								document.getElementById(id).classList.remove('md-primary')
							},(response)=>{
								this.errorMessage = 'On ne peut pas aimer son propre post'
							})
							
							
							break
						case 406:
							this.errorMessage= 'Le profil courant a liké plus de 200 post durant les 60 dernières minutes. (Securité Anti-Bot)'
							break
						
					} 

				})
				
				
			},
			

		}

	}
</script>

<style scoped>
	
	.avatar_poster{
		text-align: left;
	}

	#infosPost{
		display: inline-flex;

	}
	#infosPost span{
		margin-top: 13px;
		margin-right: 5px;
		font-size: 2em;
	}

	li{
		text-decoration: none;

	}
	.post{
		width: 500px;

	}
</style>