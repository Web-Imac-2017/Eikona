<template>
	<md-layout>
		<profileSwitch class="switchButton"></profileSwitch>
		<md-tabs md-fixed class="md-transparent" @change="onChange">
			<md-tab md-label="Tendances" id="thread-popular" class="threads">
				<thread :eventDatas="popPostsStore" :isEvents="false" @more="nextPopPosts"></thread>
			</md-tab>

			<md-tab md-label="Abonnements" id="thread-feed" class="threads">
				<thread :eventDatas="feedStore" :isEvents="true" @more="nextFeedEvents"></thread>
			</md-tab>

			<md-tab md-label="Mon profil" id="thread-profile" class="threads">
				<div class="threads"></div>
				<pageProfile :ID="-1" :current="true"></pageProfile>
			</md-tab>
		</md-tabs>
	</md-layout>
</template>


<script>
import thread from './Thread.vue'
import profileSwitch from './ProfileSwitch.vue'
import pageProfile from './ProfilePage.vue'
import store from './PostStore.js'
import Vuex from 'vuex'

export default{
	name: 'mainPage',
	store: store,
	components: {
		thread,
    	profileSwitch,
    	pageProfile
	},
	computed: {
		...Vuex.mapGetters({
			popPostsStore: 'popularPosts',
			feedStore: 'feedEvents'
		})
	},
	methods: {
		...Vuex.mapActions({
			nextPopPosts: 'nextPopularPosts',
			nextFeedEvents: 'nextFeedEvents'
		}),
		onChange (idTab) {
			switch (idTab) {
				case 0:
					if (this.popPostsStore.length === 0) this.nextPopPosts(10, '')
					break
				case 1:

					break
				case 2:
					if (this.feedStore.length === 0) this.nextFeedEvents(10)
					break
				case 3:

					break
				default:
			}
		}
	}
}
</script>

<style>
.threads {
	background-size: cover;
	background-attachment: fixed;
	background-position: top right;
	background-color: transparent;
	width: 100vw;
	min-height: 100vh;
}
#thread-popular {
	background-image: url("./../assets/bg.jpg");
}
#thread-feed{
	background-image: url("./../assets/bg3.jpg");
}
/*#thread-profile{
	background-image: url("./../assets/bg4.jpg");
}*/


.switchButton {
	position: fixed;
	top: 15%;
	right: 10%;
  z-index: 1;
}
</style>
