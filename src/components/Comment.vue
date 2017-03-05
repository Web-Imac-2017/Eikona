<template>
	<div id="commentaire">
		<div id="contenu">{{content}}</div>
		<button id="likeButton" @click="addLike">J'aime</button>
		<button id="commentReponse" @click="addReponse">Repondre</button>
	</div>
</template>

<script>
export default {
	name: "comment"
	
}

</script>