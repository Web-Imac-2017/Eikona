<template>
	<div>
		<md-menu md-direction="bottom right" md-size="5" v-if="posteurID == user.userId">
			<md-button md-menu-trigger class="md-icon-button"><md-icon>more_horiz</md-icon></md-button>
			<md-menu-content>			   
			   <md-menu-item>Modifier</md-menu-item>
			   <md-menu-item @click.native="openDialog('dialog3')">Supprimer</md-menu-item>
			</md-menu-content>
		</md-menu>
		<md-menu md-direction="bottom right" md-size="5" v-else>
			<md-button md-menu-trigger class="md-icon-button"><md-icon>more_horiz</md-icon></md-button>
			<md-menu-content>
			   
			   <md-menu-item @click.native="openDialog('dialog1')">S'abonner</md-menu-item>
			   <md-menu-item @click.native="openDialog('dialog2')">Bloquer l'utilisateur</md-menu-item>
			   <md-menu-item>Signaler ce post</md-menu-item>	
			   
			   
			</md-menu-content>
		</md-menu>
		<md-dialog  ref="dialog1" >
				  <md-dialog-title>Notifications</md-dialog-title>

				  <md-dialog-content>Souhaitez vous recevoir des notifications de ce profil ?</md-dialog-content>

				  <md-dialog-actions>
				    <md-button class="md-primary" @click.native="abonne('dialog1', 0)">Non</md-button>
				    <md-button class="md-primary" @click.native="abonne('dialog1', 1)">Oui</md-button>
				  </md-dialog-actions>
		</md-dialog>
		<md-dialog  ref="dialog2" >
				  <md-dialog-title>Bloquer un utilisateur</md-dialog-title>

				  <md-dialog-content>Souhaitez vous vraiment bloquer cette utilisateur ? Vous ne verrez aucun de ses post ou de ses commentaires et il ne pourra plus commenter vos post</md-dialog-content>

				  <md-dialog-actions>
				    <md-button class="md-primary" @click.native="close('dialog2')">Non</md-button>
				    <md-button class="md-primary" @click.native="bloque('dialog2')">Oui</md-button>
				  </md-dialog-actions>
		</md-dialog>
		<md-dialog  ref="dialog3" >
				  <md-dialog-title>Supprimer le post</md-dialog-title>

				  <md-dialog-content>Souhaitez vous vraiment supprimer ce post ?</md-dialog-content>

				  <md-dialog-actions>
				    <md-button class="md-primary" @click.native="close('dialog3')">Non</md-button>
				    <md-button class="md-primary" @click.native="supprime('dialog3')">Oui</md-button>
				  </md-dialog-actions>
		</md-dialog>
		
	</div>
</template>


<script>
import VueX from 'vuex'
import store from './connectionStore.js'

export default{
	name:'PostSettings',
	store: store,
	props:{
		postID:{
			type: Number
		},
		posteurID:{
			type: Number
		}	

	},
	computed:{
		...VueX.mapGetters({
			user: 'getUser'
		}) 
	},

	methods:{
		openDialog(ref) {
        	this.$refs[ref].open();
    	},
    	closeDialog(ref) {
      		this.$refs[ref].close();
    	},
    	abonne(ref, subscribe){
    		
    		this.$http.get('/Eikona/do/profile/follow/' + this.posteurID + '/' + subscribe).then((response) =>{
    			closeDialog(ref)
    		},(response)=>{
    			switch (response.status) {
	    			case 400 :
	    				console.log('La variable GET' + posteurID + 'n\'est pas un ID')
	    				break
	    			case 401 :
	    				console.log('Il n\'y a pas de profile connecté OU Vous n\'avez pas les droits sur ce profil OU Vous ne pouvez pas vous suivre vous-même')
	    				break
	    			case 409 :
	    				console.log('Vous suivez déjà ce profil')
	    				break
	    		}
    		})

    	},
    	bloque(ref){
    		this.$http.post('/Eikona/do/block/' + this.userID +'/' + this.posteurID).then((response)=>{
    			closeDialog(ref)
    		},(response)=>{
    		})
    	},
    	supprime(ref){
    		this.$http.get('/Eikona/do/post/delete/' + this.postID).then((response)=>{
    			closeDialog(ref)
    		},(response)=>{
    			console.log('Le post spécifié n\'existe pas')
    		
    		})
    	}


	}
}
</script>