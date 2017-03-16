<template lang="html">
	<md-layout md-column md-gutter md-flex="20" md-flex-offset="10">
		<md-whiteframe class="infos" md-elevation="8" v-if="!modification">
			<md-layout md-gutter class="cls_1">
				<md-avatar class="md-large avatar">
					<img src="../../assets/Eiko.png"/>
				</md-avatar>
				<md-layout md-gutter md-flex="50" md-list>
					<p class="profile-name">{{ profile.profileName }}</p>
					<md-button class="md-primary md-raised" @click.native="modification = !modification">Modifier</md-button>
				</md-layout>
			</md-layout>

			<md-layout md-column class="cls_2">
				<p class="infoNumber"><span>{{ profile.nbPosts }}</span> posts <span>{{ followings.length }}</span> abonnnements <span>{{ followers.length }}</span> abonnés</p>
				<p class="description"><span>Description</span><br>{{ profile.profileDesc }}</p>
			</md-layout>
		</md-whiteframe>

		<md-whiteframe type="form" class="edition" md-elevation="8" v-else @submit.stop.prevent="save">
			<md-layout md-align="end">
				<md-switch v-model="isPrivate" id="my-test1" name="my-test1" class="md-primary" @change="changeStatut">Visibilité du profil.</md-switch>
			</md-layout>
			<md-input-container>
		    	<label>Nom du profil</label>
		    	<md-input v-model="newNameModel" placeholder="profile.profileName"></md-input>
			</md-input-container>
			<md-layout class="profile_pic">
				<md-avatar class="md-large">
					<img src="../../assets/Eiko.png"/>
				</md-avatar>

				<md-input-container class="cls_file">
					<label>Photo du profil</label>
					<md-file v-model="onlyImages" accept="image/*"></md-file>
				</md-input-container>
			</md-layout>

			<md-input-container>
				<label>Description</label>
				<md-textarea v-model="newDescModel" placeholder="profile.profileDesc"></md-textarea>
			</md-input-container>

			<md-layout class="buttons" md-align="end">
				<md-button class="md-primary md-raised" type="submit">Enregistrer</md-button>
				<md-button class="md-raised">Annuler</md-button>
			</md-layout>
		</md-whiteframe>
	</md-layout>
</template>

<script type="text/javascript">
export default{
	name: 'infosEditable',
	data () {
		return {
			modification: false,
			newNameModel: '',
			newDescModel: '',
			isPrivate: false
		}
	},
	props: ['profile', 'followers', 'followings'],
	mounted () {
		console.log("InfosProfilCourant : ", this.profile)
	},
	methods: {
		save () {
			if(this.profile.profileDesc !== this.newDesc) {
				this.modifDesc (this.newDesc)
			}
			if(this.profile.profileName !== this.newName) {
				this.modifNom (this.newName)
			}
			if (this.profile.profileIsPrivate !== this.isPrivate) {
				this.modifStatut(this.isPrivate)
			}
		},
		modifNom (nom) {
			this.$http.post(apiRoot + 'profile/update/NAME/' + this.profile.profileID, {
					newValue: nom
				}).then( response => {
					console.log('MODIFICATION NAME SUCCESS', response)
					this.profile.profileName = nom
				}, (response) => {
					console.log('MODIFICATION NAME ERROR', response)
					switch (response.status) {
			          case 200:
			            console.log('Nom modifiee')
			            break
			          case 400:
			            this.error_message = ' La variable GET profileID n\'est pas un ID OU La variable POST newValue est absente'
			            break
			          case 401:
			            this.error_message = 'Vous n\'êtes pas autorisé à mettre à jour ce profil'
			            break
			          case 404:
			            this.error_message = ' Le profil spécifié n\'existe pas'
			            break
			          case 405:
			            this.error_message = ' Le field spécifié n\'est pas supporté'
			            break
			          default:
			            console.log('Unknown error')
        			}
				})
		},
		modifDesc (desc) {
			this.$http.post(apiRoot + 'profile/update/DESCRIPTION/' + this.profile.profileID, {
					newValue: desc
				}).then( response => {
					console.log('MODIFICATION DESC SUCCESS', response)
					this.profile.profileDesc = desc
				}, (response) => {
					console.log('MODIFICATION DESC ERROR', response)
					switch (response.status) {
			          case 200:
			            console.log('Desc modifiee')
			            break
			          case 400:
			            this.error_message = ' La variable GET profileID n\'est pas un ID OU La variable POST newValue est absente'
			            break
			          case 401:
			            this.error_message = 'Vous n\'êtes pas autorisé à mettre à jour ce profil'
			            break
			          case 404:
			            this.error_message = ' Le profil spécifié n\'existe pas'
			            break
			          case 405:
			            this.error_message = ' Le field spécifié n\'est pas supporté'
			            break
			          default:
			            console.log('Unknown error')
        			}
				})
		},
		modifStatut (isPrivate) {
			this.$http.post(apiRoot + 'profile/update/SETPRIVATE/' + this.profile.profileID, {
					newValue: isPrivate
				}).then( response => {
					console.log('MODIFICATION NAME SUCCESS', response)
					this.profile.profile = isPrivate
				}, (response) => {
					console.log('MODIFICATION NAME ERROR', response)
					switch (response.status) {
			          case 200:
			            console.log('Statut modifiee')
			            break
			          case 400:
			            this.error_message = ' La variable GET profileID n\'est pas un ID OU La variable POST newValue est absente'
			            break
			          case 401:
			            this.error_message = 'Vous n\'êtes pas autorisé à mettre à jour ce profil'
			            break
			          case 404:
			            this.error_message = ' Le profil spécifié n\'existe pas'
			            break
			          case 405:
			            this.error_message = ' Le field spécifié n\'est pas supporté'
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
	top:0;
	left:0;
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

.edition{
	padding: 15px;
}

.profile_pic{
	margin: 8px;
	margin-bottom: 25px;
}

.cls_file{
	display: inline;
	width: 75%;
	margin:auto;
}


</style>
