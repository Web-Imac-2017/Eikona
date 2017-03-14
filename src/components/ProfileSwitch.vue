<template lang="html">
	<md-layout>
		<span class="notification-counter" value="1">1</span>
		<md-menu md-direction="bottom left" md-size="7">
		  <md-button md-menu-trigger>
		  	<md-avatar class="md-large">
			  <img src="../../assets/arbre_bleu.jpg" alt="People">
			</md-avatar>
		  </md-button>

		  <md-menu-content>
				<md-list class="md-triple-line">
					<profile v-for="(item, i) in profiles" :profile="item" :key="item" :index="i" :extended="true" @select="select"></profile>
					<md-list-item class="md-inset">
						<span>Ajouter un profil</span>
						<md-button id="profilCreation-button" @click.native="createProfile('dialog')" class="md-icon-button md-list-action">
							<md-icon class="md-accent">add_circle</md-icon>
						</md-button>
					</md-list-item>
				</md-list>
		  </md-menu-content>
		</md-menu>
	</md-layout>
</template>


<script>
import Vuex from 'vuex'
import store from './connectionStore.js'
import profile from './Profile.vue'
import profileCreation from './Profile-creation.vue'

export default {
  name: 'profileSwitch',
  /*store: store,*/
  components: {
		profile,
    profileCreation
  },
	computed: {
		...Vuex.magGetters([
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
      this.$router.push('/user')
    }
  }
}
</script>

<style lang="css" scoped>
.md-large {
	box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
}
.md-button {
	height: 100px;
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
