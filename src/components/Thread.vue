<template lang="html">
  <md-list>
    <template v-for="p in postsData">
      <md-list-item>
        <post :post="p"></post>
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

export default {
  name: 'thread',
  props: ['postsData'],
  components: {
    post
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
