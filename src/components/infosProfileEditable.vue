<template lang="html">
	<md-layout md-column md-gutter md-flex="20" md-flex-offset="10">
		<md-whiteframe class="infos" md-elevation="8" v-if="!modification">
			<md-layout md-gutter class="cls_1">
				<md-avatar class="md-large avatar">
					<img src="../../assets/Eiko.png"/>
				</md-avatar>
				<md-layout md-gutter md-flex="50" md-column>
					<p class="profile-name">{{ user.profileName }}</p>
					<md-button class="md-primary md-raised" @click.native="modifier">Modifier</md-button>
				</md-layout>
			</md-layout>

			<md-layout md-column class="cls_2">
				<p class="infoNumber"><span>{{ user.nmb_posts }}</span> posts <span>{{ user.nmb_abonnements }}</span> abonnnements <span>{{ user.nmb_abonnés }}</span> abonnés</p>
				<p class="description">Description<br>{{ user.profileDesc }}</p>
			</md-layout>
		</md-whiteframe>	
		
		<md-whiteframe class="edition" md-elevation="8" v-else @submit.stop.prevent="submit">
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
		    	<label>Nom du profil</label>
		    	<md-input v-model="user.profileName"></md-input>
			</md-input-container>

			<md-input-container>
				<label>Description</label>
				<md-textarea v-model="user.profileDesc"></md-textarea>
			</md-input-container>
			<md-layout class="buttons">
				<md-button class="md-primary md-raised" type="submit" @click.native="save">Enregistrer</md-button>
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

span{
	font-weight: 500;
}

.infoNumber{
	padding: 5px;
	margin-bottom: 10px;
	text-align: center;
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