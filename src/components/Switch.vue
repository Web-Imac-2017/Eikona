<template lang="html">
	<div>
		<span class="notification-counter" value="1">1</span>
		<md-menu md-direction="bottom left" md-size="7">
		  <md-button md-menu-trigger>
		  	<md-avatar class="md-large">
			  <img src="../../assets/arbre_bleu.jpg" alt="People">
			</md-avatar>
		  </md-button>

		  <md-menu-content>
		  	<div class="author-card">
		    	<md-menu-item>
		    		<md-avatar class="md-large">
		  				<img src="../../assets/arbre_bleu.jpg" alt="People">
					</md-avatar>
					<span>profil actuel</span>
					<md-badge>1</md-badge>
				</md-menu-item>
			</div>

			<div class="author-card">
		   		<md-menu-item>
		    		<md-avatar class="md-large">
		  				<img src="../../assets/arbre_violet.jpg" alt="People">
					</md-avatar>
					<span>profi2</span>
				</md-menu-item>
			</div>

			<div class="author-card">
		    	<md-menu-item>
		    		<md-avatar class="md-large">
		  				<img src="../../assets/arbre_violet.jpg" alt="People">
					</md-avatar>
					<span>profil3</span>
				</md-menu-item>
			</div>
		  </md-menu-content>
		</md-menu>
	</div>
</template>




<script>

export default {
  name: 'profileSwitch'
}

	
</script>




<style lang="css" scoped>

.md-large {
	box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
}

.md-button {
	height: 100px;
}

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

.notification-counter {   
    position: absolute;
    top: 15px;
    right: 22px;
    z-index: 2;
    background-color: #f0f3bd;
    color: #000;
    border-radius: 2px;
    padding: 3px 6px;
}


</style>