<template>
	<md-layout>
		<div v-if="errorMessage != ''" class="error-msg">{{ errorMessage }}</div>
		<md-card class='post'>
			<md-card-header>
				<md-layout>
					<md-layout md-align="start" class="avatar_poster">
						<md-avatar>
					  		<img :src="profile.profile_picture" alt="Avatar">
						</md-avatar>
						<span>{{profile.profile_name}}</span>
					</md-layout>
					<md-layout md-align="end">
						<PostSettings class="md-list-action"></PostSettings>
					</md-layout>
				</md-layout>
			</md-card-header>

	 		<md-card-media>
				<img :src="imageLink" alt="Photo test">
  		</md-card-media>

  		<md-layout class="infosPost" >
  			<md-layout md-align="start">
					<div class="md-title">{{title}}</div>
				</md-layout>
				<md-layout md-align="end">
					<md-button id='post-Like' class="md-icon-button" @click.native="addLike('post-Like')"><md-icon>favorite</md-icon> </md-button>
					<span>{{like}}</span>
				</md-layout>
			</md-layout>

			<md-chips v-model="tags" md-static>
 				 <template scope="chip">{{ chip.value }}</template>
			</md-chips>

			<md-card-content>
				<div class="description">{{post.post_description}}</div>
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
				// Normalement, il ne devrait plus y avoir de data quand tout sera dynamique
				imageLink: 'assets/testPhoto.jpg',
				title: 'Enleve moi ce titre, il n\'en a pas dans la base de donnée',

				// A quoi elle sert ?
				newComment: '',

				errorMessage: ''
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
			comments () {
				// Recuperer les commentaires du post
				return [{
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
					}]
			},
			like () {
				// Récupérer le nombre de like du post
				return 0
			},
			tags () {
				// Récpérer les tags attachés à ce post
				return ['chevals', 'ornithorynque']
			}
		},
		methods: {
			addLike (id) {
				this.$http.get(apiRoot + 'post/like/'+this.post.post_id).then((response) => {
					this.nbrLike++
					document.getElementById(id).classList.add('md-primary')
				},(response)=>{
					switch (response.status) {
						case 401:
							this.errorMessage = 'Le post spécifié n\'existe pas OU l\'user n\'a pas de profil courant OU vous ne suivez pas la personne'
							break
						case 400:
							this.$http.get(apiRoot + 'post/unlike/'+this.post.post_id).then((response)=>{
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
			}
		}
	}
</script>

<style scoped>
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
		font-size: 2em;
	}
	li{
		text-decoration: none;
	}
	.post{
		width: 500px;
	}
</style>
