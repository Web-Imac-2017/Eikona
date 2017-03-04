<template>
	<md-layout id="infosProfil">
		<md-layout class="cls_1">
			<md-avatar class="md-large">
				<img src="../../assets/portrait_final.jpg"/>
			</md-avatar>
			<md-layout>
				<p class="profile-name">{{ user.profileName }}</p>
				<md-button v-if="!abonne" class="md-raised md-primary" @click.native="sabonner">S'abonner</md-button>
				<md-button v-else class="md-raised" @click.native="sabonner">Abonné(e)</md-button>
			</md-layout>
		</md-layout>

		<md-layout class="cls_2">
			<p class="infoNumber">{{ user.nmb_posts }} posts</p> 
			<p class="infoNumber">{{ user.nmb_abonnements }} abonnnements</p>
			<p class="infoNumber">{{ user.nmb_abonnés }} abonnés</p>
		</md-layout>
		<p class="description">{{ user.profileDesc }}</p>
	</md-layout>
</template>

<script>
import connection from './Connection.vue'

export default {
	name: 'informationsProfilAutre',
	data () {
		return {
			connected: true,
			abonne: false
		}
	},
	computed :{
		user () {
			return{
				nmb_posts: 30,
				nmb_abonnements: 300,
				nmb_abonnés: 6000,
				profileName: 'nom_du_profil',
				profileDesc: 'Lorem ipsum dolor sit amet. Blablibla blou blabli bloublou.'
			}
		}
	},
	methods: {
		sabonner () {
			if(!this.connected){
				console.log("!connected");
			}
			else{
				console.log("changement");
				this.abonne=!this.abonne;
			}	
		}
	}
}

</script>

<style scoped>

#infosProfil{
	width: 30%;
	padding: 30px;
	background-color: white;
	display: block;
	margin: 50px;
	border: 1px solid black;
}

p{
	font-family: 'Roboto';
	font-weight: 300;
}

.profile-name{
	font-weight: 500;
}

.cls_1{
	width: 100%;
	display: flex;
}

.cls_2{
	width: 100%;
}

p.infoNumber{
	display: inline;
}

</style>
