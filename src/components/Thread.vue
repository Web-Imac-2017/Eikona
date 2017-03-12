<template lang="html">
  <md-list>
    <template v-for="item in list" :key="item">
      <md-list-item>
        <p>{{ eventMessage(item) }}</p>
        <profile v-if="p.type == 'follow'" :profile="p.profileData" :index="-1" :extended="false"></profile>
        <post v-else :post="p.postData" :profilePost="p.profileData"></post>
      </md-list-item>
    </template>
    <md-list-item @click.native="more" class="thread-more">
      <p>Afficher plus</p>
      <md-icon>keyboard_arrow_down</md-icon>
    </md-list-item>
  </md-list>
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
      this.eventDatas.forEach(item => l.push({
        type: 'post',
        profileData: item.profileData,
        postData: item
      }))
      return newList
    },
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
    }
  },
  methods: {
    more: () => this.$emit('more', 10)
  }
}
</script>

<style lang="css">
.thread-more {
  text-align: center;
}
</style>
