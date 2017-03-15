<template lang="html">
  <md-layout class="thread">
    <md-list class="thread-list">
      <template v-for="item in list" :key="item">
        <md-list-item class="thread-item">
          <p>{{ eventMessage(item) }}</p>
          <profile v-if="item.type == 'follow'" :profile="item.profileData" :index="-1" :extended="false"></profile>
          <post v-else :post="item.postData" :profilePost="item.profileData"></post>
        </md-list-item>
      </template>
      <md-list-item @click.native="more" class="thread-more" v-if="list.length > 0">
        <p>Afficher plus</p>
        <md-icon>keyboard_arrow_down</md-icon>
      </md-list-item>
      <md-list-item v-else>
        <p>Aucun post à afficher</p>
      </md-list-item>
    </md-list>
  </md-layout>
</template>

<script>
import post from './Post.vue'
import profile from './Profile.vue'

export default {
  name: 'thread',
  props: {
    eventDatas: Array,
    isEvents: Boolean
  },
  components: {
    post,
    profile
  },
  computed: {
    list () {
      if(this.isEvents) return this.eventDatas
      var newList = []
      this.eventDatas.forEach(item => newList.push({
        type: 'post',
        profileData: item.data.profileData,
        postData: item.data
      }))
      return newList
    },
    postsLoadedIds () {
      var ex = ''
      if (this.list.length > 0) {
        ex = this.list.shift().postID
        this.list.forEach(i => {ex += (',' + i.postID)})
      }
      return ex
    }
  },
  methods: {
  eventMessage (e) {
    switch (e.type) {
      case 'comment':
        return e.profileData.profileName + ' a commenté votre publication : '
      case 'like':
        return e.profileData.profileName + ' a aimé votre publication : '
      case 'follow':
        return e.profileData.profileName + ' s\'est abonné à votre profile.'
      default:
        return ''
    }
  },
    more () {this.$emit('more', 10, this.postsLoadedIds)}
  }
}
</script>

<style lang="css">
.thread-list {
  margin: 0 auto;
  background-color: rgba(255, 255, 255, 0.2);
}
.thread-more {
  text-align: center;
}
.thread-item {
  margin: 20px 0;
}
</style>
