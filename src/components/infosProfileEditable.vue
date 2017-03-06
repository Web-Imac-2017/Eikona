<template lang="html">
	<md-layout>
		<md-whiteframe class="infos" md-elevation="8" v-if="!modification">
			<md-layout class="cls_1">
				<md-avatar class="md-large">
					<img src="../../assets/Eiko.png"/>
				</md-avatar>
				<p class="profile-name">{{ user.profileName }}</p>
				<md-button class="md-primary md-raised" @click.native="modifier">Modifier</md-button>
			</md-layout>
			<md-layout class="cls_2">
				<p class="infoNumber"><span>{{ user.nmb_posts }}</span> posts <span>{{ user.nmb_abonnements }}</span> abonnnements <span>{{ user.nmb_abonnés }}</span> abonnés</p>
				<p class="description">{{ user.profileDesc }}</p>
			</md-layout>
		</md-whiteframe>	
		
		<md-whiteframe class="infos" md-elevation="8" v-else @submit.stop.prevent="submit">
			<md-avatar class="md-large">
				<img src="../../assets/Eiko.png"/>
			</md-avatar>
		    <md-input-container>
				<label>Photo du profil</label>
				<md-file v-model="onlyImages" accept="image/*"></md-file>
			</md-input-container>

			<md-input-container>
		    	<label>Nom du profil</label>
		    	<md-input v-model="user.profileName"></md-input>
			</md-input-container>

			<md-input-container>
				<label>Description</label>
				<md-textarea v-model="user.profileDesc"></md-textarea>
			</md-input-container>

			<md-button class="md-primary md-raised" type="submit" @click.native="save">Enregistrer</md-button>
			<md-button class="md-raised">Annuler</md-button>
		</md-whiteframe>
	</md-layout>
</template>

<script type="text/javascript">
export default{
	name: 'infosEditable',
	data () {
		return {
			modification: false,
			nameProfile: '',
			descProfile: '',
			statusProfile:'',
			avatarProfile:''
		}
	},
	computed :{
		user(){
			return {
				nmb_posts: 30,
				nmb_abonnements: 300,
				nmb_abonnés: 6000,
				profileName: 'nom_du_profil',
				profileDesc: 'Lorem ipsum dolor sit amet. Blablibla blou blabli bloublou.'
			}
		}
	},
	methods: {
		submit () {
			console.log('submit');
		},
		save () {
			console.log('save');
			this.modification = !this.modification;
		},
		modifier () {
			this.modification = !this.modification;	
		}
	}
}
	
</script>

<style scoped>

p{
	font-family: 'Roboto';
	font-weight: 300;
}

.profile-name, span{
	font-weight: 500;
}

.cls_1{
	background-color: orange;
	margin: auto;
}

.cls_2{
	background-color: lightblue;
	display: block;
}



</style>