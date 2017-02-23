<template lang="html">
  <div id="search-bar">
    <button @click="search"></button>
    <input name="search" type="search" placeholder="Mots-clés" />
  </div>
</template>

<script>
export default {
  methods: {
    search () {
      console.log('Search')
    }
  }
}
</script>

<style lang="css" scoped>
#search-bar {
  margin-left : 1%;
}
</style>
