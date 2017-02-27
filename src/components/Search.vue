<template lang="html">
  <div>
    <form novalidate @submit.stop.prevent="search">
      <md-input-container id="search-bar" md-inline>
        <md-icon md-primary>search</md-icon>
        <label>Rechercher...</label>
        <md-input type="search" v-model="keywords"></md-input>
      </md-input-container>
    </form>
  </div>
</template>

<script>
export default {
  data () {
    return {
      keywords: ''
    }
  },
  methods: {
    search () {
      console.log('Search : ', this.keywords)
    }
  }
}
</script>

<style lang="css" scoped>
  form {
    width: 80%;
    margin: 0 10%;
  }
</style>
