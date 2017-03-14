<template lang="html">
	<div>
		<span class="notification-counter" value="1">1</span>
		<md-menu md-direction="bottom left" md-size="7">
		  <md-button md-menu-trigger>
		  	<md-avatar class="md-large">
			  <img src="../../assets/arbre_bleu.jpg" alt="People">
			</md-avatar>
		  </md-button>

		  <md-menu-content>
		  	<currentItem></currentItem>
		  	<otherItem></otherItem>
		  	<addItem></addItem>
		  </md-menu-content>
		</md-menu>
	</div>
</template>


<script>
import currentItem from './ProfileSwitchCurrentItem.vue'
import otherItem from './ProfileSwitchOtherItem.vue'
import addItem from './ProfileSwitchAddItem.vue'
/*import Vuex from 'vuex'
import store from './connectionStore.js'
import profile from './Profile.vue'
import profileCreation from './Profile-creation.vue'*/

export default {
  name: 'profileSwitch',
  /*store: store,*/
  components: {
    currentItem ,
    otherItem ,
    addItem
  },
 /* data () {
    return {
      creationForm: false
    }
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
    createProfile () {
      this.creationForm = true
    },
    select (profileId) {
      this.selectProfile(profileId)
      this.$router.push('/user')
    }
  }*/
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