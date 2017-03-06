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
				<img src="assets/testPhoto.jpg" alt="Photo test">
	  		</md-card-media>
	  		<div class="md-title">{{title}}</div>
		  	<md-card-actions>
				<md-button @click.native="addLike">J'aime : {{nbrLike}}</md-button>
				
			</md-card-actions>
			<md-card-content>
				<input type="text" class="new-comment" placeholder="Ajouter un commentaire" @keyup.enter="addComment" v-model="newComment">
				<ul class="comments-list">
					<li class="comment" v-for="comment in comments">				
						<label>{{comment.message}}</label>
						<md-button :click.native="addLikeComment">J'aime {{comment.nbrLikeComment}}</md-button>
						<md-button >Repondre</md-button>
					</li>
				</ul>
			</md-card-content>
		</md-card>
	
	</div>
</template>



<script>
	export default{
		name : "PostFront",

		data () {
			return {
				userName : "JackieDu29",
				nbrLike : 0,
				nbrComment : 0,
				nbrJour : 0,
				title: 'Look at my mustach',
				comments: [],
				newComment: ''

			}
		},

		methods: {
			addLike () {
				this.nbrLike++
			},
			addComment(){
				this.comments.push({
					message: this.newComment,
					nbrLikeComment: 0
				})
				this.newComment=""
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