<template lang="html">
  <form id="search-bar">
    <button @click="search"></button>
    <input type="text" placeholder="Mots-clés" />
  </form>
</template>

<script>
export default {
  methods : {
    search () {
      console.log("Search");
    }
  }
};
</script>

<style lang="css" scoped>
</style>
