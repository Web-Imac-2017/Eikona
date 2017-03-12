<template>
	<md-tabs md-fixed class="md-transparent">
		<md-tab md-label="Tendances">
			<thread :postsData="popPostsStore" @more="nextPopPosts"></thread>
		</md-tab>

		<md-tab md-label="Suggestions">
		</md-tab>

		<md-tab md-label="Abonnements">
				<thread :postsData="feedStore" @more="nextFeedEvents"></thread>
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
	mounted: () => {
		this.nextPopPosts(10),
		this.nextFeedEvents(10)
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
		})
	}
}
</script>
