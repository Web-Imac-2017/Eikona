<template lang="html">
  <md-list>
    <md-list-item v-for="p in posts">
      <post :post="p"></post>
    </md-list-item>
    <md-list-item @click.native="more">
      <p>Afficher plus</p>
      <md-icon>keyboard_arrow_down</md-icon>
    </md-list-item>
  </md-list>
</template>

<script>
import post from './Post.vue'

export default {
  name: 'thread',
  props: ['posts'],
  components: {
    post
  },
  methods: {
    more: () => this.$emit('more', 10)
  }
}
</script>

<style lang="css">
</style>
