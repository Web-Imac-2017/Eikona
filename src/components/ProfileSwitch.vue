<template lang="html">
	<md-layout>
    <span id="notification-counter">!</span>
		<md-menu md-direction="bottom left" md-size="7">
			<md-avatar class="md-large" md-menu-trigger>
				<img src="../assets/arbre_bleu.jpg" alt="People">
			</md-avatar>

		  <md-menu-content>
				<md-list class="md-triple-line">
          <md-subheader>Profil(s)</md-subheader>
					<profile v-for="(item, i) in profiles" :profile="item" :key="item" :index="i" :extended="false" @select="select"></profile>
          <md-list-item class="md-inset">
            <span>Ajouter un profil</span>
            <md-button id="profilCreation-button" @click.native="createProfile('dialog')" class="md-icon-button md-list-action">
              <md-icon class="md-accent">add_circle</md-icon>
            </md-button>
          </md-list-item>
          <md-divider></md-divider>
          <md-subheader>Notification(s) non lue(s)</md-subheader>
          <!--<div class="md-list-text-container">
            <span>{{  }}</span>
            <p v-show="extended">{{  }}</p>
            <p>{{  }}</p>
          </div>-->
          
            <md-list-item v-for="notif in notifs">
              <span>{{ getNotifMessage(notif) }}</span>
            </md-list-item>

          </md-list>
		  </md-menu-content>
		</md-menu>

    <md-dialog md-open-from="profilCreation-button" md-close-to="profilCreation-button" ref="dialog">
      <md-dialog-title>Nouveau profil</md-dialog-title>
      <md-dialog-content>
        <profileCreation @close="closeCreation('dialog')"></profileCreation>
      </md-dialog-content>
    </md-dialog>
	</md-layout>
</template>


<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import profile from './Profile.vue'
import profileCreation from './Profile-creation.vue'
import apiRoot from './../config.js'



export default {
  name: 'profileSwitch',
	store: store,
  components: {
    profile,
    profileCreation
  },
  data () {
    return {
      notifs: []
    }
  },
  mounted () {
    this.getNotification()
  },
  computed: {
    ...Vuex.mapGetters([
      'getUser',
      'profiles'
    ])
  },
  methods: {
    ...Vuex.mapActions([
      'selectProfile'
    ]),
    createProfile (ref) {
      this.$refs[ref].open()
    },
    closeCreation (ref) {
      this.$refs[ref].close()
    },
    select (profileId) {
      this.selectProfile(profileId)
      this.$emit('change')
    },
    getNotifMessage(notif) {
      switch (notif.notif_type) {
        case "1":
          return 'Un utilisateur aimerait vous suivre !'
          break
        case "2":
          return 'Vous pouvez désormais suivre ce profil'
          break
        case "3":
          return 'Vous avez un nouveau abonné'
          break
        case "4":
          return 'Une personne a aimé votre post'
          break
        case "5":
          return 'Une personne a commenté votre post'
          break
        case "6":
          return 'Une personne a aimé votre commentaire'
          break
        default:
          return 'message '
      }
    },
    getNotification() {
      this.$http.get(apiRoot+'profile/notifications/').then((response) => {
        console.log('SUCCESS: notification recuperee', response)
        this.notifs = response.data.data.notif
     }, (response) => {
      console.error('ERR: get notification request', response)
      switch (response.status) {
        case 401:
          console.error('Unauthorized')
          break
        case 404:
          console.log('Not foud')
          break
        default:
          console.log('Unknown error')
        }
      })
    }
  }
}

  
    
</script>

<style lang="css" scoped>
.md-large {
	box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
  cursor: pointer;
}
.md-button {
	height: 100px;
}
#notification-counter {
    position: absolute;
    top: -3px;
    right: -2px;
    z-index: 2;
    background-color: #f0f3bd;
    color: #000;
    border-radius: 2px;
    padding: 3px 6px;
}
</style>
