<template>
	<md-layout md-column md-flex="20" md-flex-offset="10">
		<md-whiteframe class="infos" md-elevation="8">
			<md-layout md-gutter class="cls_1">
				<md-avatar class="md-large avatar">
					<img src="./assets/Eiko.png"/>
				</md-avatar>

				<md-layout md-flex="50" md-column>
					<p class="profile-name">{{ profile.profileName }}</p>

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
				<p class="infoNumber"><span>{{ nmbPosts.nbrPosts }}</span> posts <span>{{ listFollowings.nbrFollowings }}</span> abonnnements <span>{{ listFollowers.nbrFollowers }}</span> abonnés</p>
				<p class="description"><span>Description</span><br>{{ currentProfile.profileDesc }}</p>
			</md-layout>
		</md-whiteframe>
	</md-layout>
</template>

<script>
import connection from './Connection.vue'
import apiRoot from './../config.js'
import store from './connectionStore.js'

export default {
	name: 'informationsProfilAutre',

	data () {
		return {
			follow: false,
			notif: false
		}
	},
	props:['profile'],
	mounted () {
		this.connection ();
		//this.follower ();
	},
	methods: {
		connection () {
			if(this.currentProfile.profileID = '') {this.connected = false;}
			else {this.connected = true;}
		},
		follower () {
			this.$http.get(apiRoot + 'profile/follow/' + this.currentProfile.profileID).then((response) => {
					{
						console.log(response);
						this.follow = response.data.data
						if(this.follow.isFollowing === 1 && this.follow.isConfirmed === 1){
							this.abonne = true
						}
						else if(this.follow.isConfirmed === 0){
							//this.$ref.open()
						}
						else if(this.follow.isSubscribed === 1){
							this.notif = true
						}
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
						default:
							console.log('Unknown error')
					}
				})
		},
		sabonner () {

			if(!this.connected){
				console.log("Retour page connexion");
				this.$router.push('/');
			}
			else{
				this.abonne=!this.abonne;
				this.notif=true;

				//requete abonnement
				this.$http.get(apiRoot + 'profile/follow/' + this.currentProfile.profileID).then((response) => {
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
						default:
							console.log('Unknown error')
					}
				})
			}
		},
		desabonner () {
			this.abonne=!this.abonne;
			this.notif=false;

			this.$http.get(apiRoot + 'profile/follow/' + this.currentProfile.profileID).then((response) => {
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
						default:
							console.log('Unknown error')
					}
				})
		},
		notifier () {
			this.$http.get(apiRoot + 'profile/subscribe/' + this.currentProfile.profileID).then((response) => {
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
						default:
							console.log('Unknown error')
					}
				})
			this.notif=!this.notif
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
