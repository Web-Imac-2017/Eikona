<template>
	<md-layout md-column md-flex="20" md-flex-offset="10">
		<md-whiteframe class="infos" md-elevation="8">
			<md-layout md-gutter class="cls_1">
				<md-avatar class="md-large avatar">
					<img src="../../assets/Eiko.png"/>
				</md-avatar>

				<md-layout md-flex="50" md-column>
					<p class="profile-name">{{ user.profileName }}</p>

					<md-layout v-if="!abonne" md-gutter>
						<md-button  class="md-raised md-primary" @click.native="sabonner">S'abonner</md-button>
						<md-button class="md-fab md-raised md-mini" disabled>
							<md-icon>notifications_none</md-icon>
						</md-button>
					</md-layout>

					<md-layout v-else md-gutter>
						<md-button  class="md-raised" @click.native="desabonner">Abonné(e)</md-button>
						<md-button v-if="!notif" class="md-fab md-raised md-mini md-clean" @click.native="notifier">
							<md-icon>notifications_none</md-icon>
						</md-button>
						<md-button v-else class="md-fab md-raised md-mini md-primary" @click.native="notifier">
							<md-icon>notifications</md-icon>
						</md-button>
					</md-layout>
				</md-layout>

			</md-layout>

			<md-layout md-column class="cls_2">
				<p class="infoNumber"><span>{{ user.nmb_posts }}</span> posts <span>{{ user.nmb_abonnements }}</span> abonnnements <span>{{ user.nmb_abonnés }}</span> abonnés</p>
				<p class="description"><span>Description</span><br>{{ user.profileDesc }}</p>
			</md-layout>
		</md-whiteframe>
	</md-layout> 
</template>

<script>
import connection from './Connection.vue'

export default {
	name: 'informationsProfilAutre',

	data () {
		return {
			abonne: follower(),
			notif: false
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
	props:{
		profile: Object,
		currentP: Object
	},
	created: {
		connection () {
			if(currentP.id = '') {connected = false;}
			else {connected = true;}
		}
		follower () {
			this.$http.get(apiRoot + '/profile/follow/' + this.currentP.profileID).then((response) => {
					{
					 	isFollowing : //1 si le followed est suivant par le profil follower, 0 sinon.
    					isSubscribed : //1 si le follower est abonné au profil followed, 0 sinon.
    					isConfirmed : //1 si l abonnement est confirmé, 0 sinon   
					}
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.$route.params.profileID + ' n\est pas un ID')
							break
						case 401:
							console.log('Il n\'y a pas de profil connecté OU Vous n\'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même')
							break
						case 409:
							console.log('Vous suivez déjà ce profil')
							break
						default
					}
				})

			if(isFollowing === 1){
				return true;
			}
		}
		//faire requete nmb posts
		//faire requete nmb followers
		//faire requete npm followings
	},
	methods: {
		sabonner () {

			if(!this.connected){
				console.log("Retour page connexion");
				this.$router.push('');
			}
			else{
				this.abonne=!this.abonne;
				this.notif=true;
				//requete abonnement
				this.$http.get(apiRoot + '/profile/follow/' + this.currentP.profileID).then((response) => {
					{
					    
					}
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.$route.params.profileID + ' n\est pas un ID')
							break
						case 401:
							console.log('Il n\'y a pas de profil connecté OU Vous n\'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même')
							break
						case 409:
							console.log('Vous suivez déjà ce profil')
							break
						default
					}
				})				
			}
		},
		desabonner () {
			this.abonne=!this.abonne;
			this.notif=false;

			this.$http.get(apiRoot + '/profile/follow/' + this.currentP.profileID).then((response) => {
					{
					    
					}
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.$route.params.profileID + ' n\est pas un ID')
							break
						case 401:
							console.log('Il n\'y a pas de profil connecté OU Vous n\'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même')
							break
						case 409:
							console.log('Vous suivez déjà ce profil')
							break
						default
					}
				})
		},
		notifier () {
			this.$http.get(apiRoot + '/profile/subscribe/' + this.currentP.profileID).then((response) => {
					{
					    
					}
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.$route.params.profileID + ' n\est pas un ID')
							break
						case 401:
							console.log('Il n\'y a pas de profil connecté OU Vous n\'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même')
							break
						case 409:
							console.log('Vous suivez déjà ce profil')
							break
						default
					}
				})
			this.notif=!this.notif;
		}
	}
}

</script>

<style scoped>

p, span{
	font-family: 'Roboto';
	font-size: 0.8vw;
	font-weight: 300;
}

.cls_1{
	padding: 15px;
	margin-bottom: 15px;
	border-bottom: 1px solid lightgrey;
	position: relative;
}

.profile-name{
	font-size: 1.2vw;
	margin: 8px;
}

.cls_2{
	padding: 15px;
}

.infoNumber{
	padding: 5px;
	margin-bottom: 10px;
	text-align: center;
}

p.infoNumber span{
	font-weight: 500;
}

p.description{
	font-weight: 500;
}

p.description span{
	font-weight: 300;
}

</style>
