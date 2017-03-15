<template lang="html">
	<div>
		<div class="author-card">
			<md-menu-item>
				<md-avatar>
						<img src="../../assets/arbre_violet.jpg" alt="People">
				</md-avatar>

				<span>Other item</span>
				<md-badge>5</md-badge>

			</md-menu-item>
		</div>
	</div>
</template>


<script>
export default {
	name: 'otherItem',
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