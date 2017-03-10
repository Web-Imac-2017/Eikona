<template>
	<div id="commentaire">
		<div id="contenu">{{content}}</div>
		<md-button id="likeButton" @click.native="addLike">J'aime {{nbrLike}}</md-button>
		<md-button id="commentReponse" @click.native="addReponse">Repondre</md-button>
	</div>
</template>

<script>
export default {
	name: "comment",
	
	props: {
		id: {
			type: Number

		},
		content: {
			type: String,
			default : '',
			required: true
		},
		nbrLike: {
			type: Number,
			default: 0,
			required: true
		}
	},
	methods: {
		addLike(){
			//this.@emit('incrementLike')
		}
	}
	
}

</script>