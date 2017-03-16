<template>
	<md-layout md-column md-flex="20" md-flex-offset="10">
		<md-whiteframe class="infos" md-elevation="8">
			<md-layout md-gutter class="cls_1">
				<md-avatar class="md-large avatar">
					<img src="./assets/Eiko.png"/>
				</md-avatar>

				<md-layout md-flex="50" md-column>
					<p class="profile-name">{{ profile.profileName }}</p>

					<md-layout md-gutter>
						<md-button  class="md-raised md-primary" @click.native="follow">{{ followButText }}</md-button>
						<md-button class="md-fab md-raised md-mini md-clean" @click.native="notifier">
							<md-icon v-if="!notif">notifications_none</md-icon>
							<md-icon v-else>notifications</md-icon>
						</md-button>
					</md-layout>
				</md-layout>

			</md-layout>

			<md-layout md-column class="cls_2">
				<p class="infoNumber"><span>{{ profile.nbPosts }}</span> posts <span>{{ followings.length }}</span> abonnnements <span>{{ followers.length }}</span> abonnés</p>
				<p class="description"><span>Description</span><br>{{ profile.profileDesc }}</p>
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
	props:['profile', 'followings', 'followers'],
	computed: {
		followButText() {
			if (this.follow) return 'Abonné'
			return 'S\'abonner'
		}
	},
	mounted () {
		this.isFollowing()
	},
	methods: {
		isFollowing () {
			this.$http.get(apiRoot + 'profile/isFollowing/' + this.profile.profileID).then((response) => {
					console.log('Profile is following : ', response)
					if (response.data.data.isSubscribed() === 1) this.follow = true
					else this.follow = false
				},(response)=>{
					switch (response.status) {
						case 400:
							console.log('La variable GET ' + this.profile.profileID + ' n\est pas un ID')
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
		follow () {
			//requete abonnement
			this.$http.get(apiRoot + 'profile/follow/' + this.profile.profileID).then((response) => {
				this.follow = !this.follow
			},(response)=>{
				switch (response.status) {
					case 400:
						console.log('La variable GET ' + this.profile.profileID + ' n\est pas un ID')
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
			this.$http.get(apiRoot + 'profile/subscribe/' + this.profile.profileID).then((response) => {
					this.notif=!this.notif
			},(response)=>{
				switch (response.status) {
					case 400:
						console.log('La variable GET ' + this.profile.profileID + ' n\est pas un ID')
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
