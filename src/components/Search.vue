<template lang="html">
  <md-layout md-align="start">
    <form @submit.stop.prevent="search" class="field-group">
      <md-layout>
        <md-layout md-flex="60">
          <md-input-container md-inline>
            <md-icon>search</md-icon>
            <label>Recherche...</label>
            <md-input type="search" v-model="keywords"></md-input>
          </md-input-container>
        </md-layout>
        <md-layout md-flex="40">
          <md-input-container>
            <label for="movie">Par :</label>
            <md-select name="movie" id="movie" v-model="searchType">
              <md-option value="all">Sur tout le site</md-option>
              <md-option value="profile">Profil</md-option>
              <md-option value="description">Publication</md-option>
              <md-option value="tag">Tag</md-option>
              <md-option value="comment">Commentaire</md-option>
            </md-select>
          </md-input-container>
        </md-layout>
      </md-layout>
    </form>
  </md-layout>
</template>

<script>
export default {
  data () {
    return {
      keywords: [],
      searchType: 'all'
    }
  },
  methods: {
    search () {
      var regex = /\s/g
      this.$router.push('/search/' + this.searchType + '/' + this.keywords.replace(regex, '+'))
    }
  }
}
</script>

<style scoped>
form {
  width: 90%;
  margin: 0 5%;
}
</style>
