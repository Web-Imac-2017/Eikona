<template>
	<div id="post">
		<div id="infoPosteur">{{userName}}</div>
		<button :click.native="addLike">ajouter un like : {{nbrLike}}</button>
		<input type="text" class="new-comment" place-holder="Ajouter un commentaire" @keyup.enter="addComment" v-model="newComment">
		<ul class="comments-list">
			<li class="comment" v-for="comment in comments">				
				<label>{{comment.message}}</label>
			</li>
		</ul>


	
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
					message: this.newComment
				})
				this.newComment=""
			}
		}

	}
</script>

<style scoped>

</style>