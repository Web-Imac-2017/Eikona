<template lang="html">
  <md-layout>
    <md-layout md-flex="10">
      <md-icon>search</md-icon>
    </md-layout>
    <md-layout>
      <md-chips md-input-type="search" v-model="keywords" md-input-placeholder="Rechercher..." @change="search">
        <template scope="chip">{{chip.value}}</template>
      </md-chips>
    </md-layout>
  </md-layout>
</template>

<script>
export default {
  data () {
    return {
      keywords: []
    }
  },
  methods: {
    search () {
      console.log('Search : ', this.keywords)
    }
  }
}
</script>
<style>

</style>
