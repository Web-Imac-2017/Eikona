<template lang="html">
	<div>
		<div class="author-card">
			<md-menu-item>
				<md-avatar class="md-large">
						<img src="../../assets/arbre_bleu.jpg" alt="People">
				</md-avatar>

				<div class="md-list-text-container">
					<span>Current item</span>
					<span>Vous avez 1 notification(s) non lue(s)</span>
				</div>
			</md-menu-item>
		</div>
	</div>
</template>


<script>
export default {
	name: 'currentItem',
}

</script>


<style type="text/css" scoped>

.author-card {
  padding: 15px 16px;
 }

.author-card .md-avatar {
 	box-shadow: none;
 }

span {
	font-size: 16px;
	display: flex;
	flex-flow: column;
	flex: 1;
}
</style>