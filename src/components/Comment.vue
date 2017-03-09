<template>
	<md-list-item>	
		<div class="content-commentaire">
			<p>{{content}}</p>
			<p>{{nbrLike}} aiment ça</p>
		</div>
	 	<md-button  class="md-icon-button md-list-action" @click.native="addLike">
	 		
	 		<md-icon>favorite</md-icon>
	 	</md-button>
	</md-list-item>
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
			this.$emit('incrementLike', this.id)
		},
		addReponse(){

		}
	}
	
}

</script>
	
<style>
	.content-commentaire  p:nth-child(2){
		color: grey;
		margin-top: 0;
		font-size: 80%;
		font-style: italic;
	}
	.content-commentaire  p:nth-child(1){
		margin-bottom: 0;
	}
</style>