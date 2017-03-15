<template lang="html">
	<div>
		<div class="author-card">
			<md-menu-item>
				<md-avatar class="md-avatar-icon md-primary">
					<md-icon>add</md-icon>
				</md-avatar>

				<span>Ajouter un nouveau profil</span>
	
			</md-menu-item>
		</div>
	</div>
</template>


<script>
export default {
	name: 'addItem',
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

md-icon {
	background-color: #454;
}
</style>