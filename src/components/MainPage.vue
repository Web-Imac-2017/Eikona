<template>
	<md-tabs md-fixed class="md-transparent" @change="onChange">
		<md-tab md-label="Tendances">
			<thread :eventDatas="popPostsStore" :isEvents="false" @more="nextPopPosts"></thread>
		</md-tab>

		<md-tab md-label="Suggestions">
		</md-tab>

		<md-tab md-label="Abonnements">
			<thread :eventDatas="feedStore" :isEvents="true" @more="nextFeedEvents"></thread>
		</md-tab>

		<md-tab md-label="Mon profil">
		</md-tab>
	</md-tabs>
</template>


<script>
import thread from './Thread.vue'
import store from './PostStore.js'
import Vuex from 'vuex'

export default{
	name: 'mainPage',
	store: store,
	components: {
		thread
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
			console.log('Tab : ' + idTab)
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
