<template lang="html">
  <md-whiteframe>
    <md-spinner id="search-loader" v-if="searching" md-indeterminate></md-spinner>
    <md-layout v-else>
      <md-layout v-if="noresult">
        <p>Nous n'avons trouvé aucun résultat correspondant à la recherche</p>
        <p>{{keywords}}</p>
      </md-layout>
      <md-layout v-else>
      </md-layout>
    </md-layout>
  </md-whiteframe>
</template>

<script>
import searchResult from './Search-result.vue'
import apiRoot from './../config.js'

export default {
  name: 'searchPage',
  components: {
    searchResult
  },
  props: ['query'],
  data: () => ({
    resultPosts: [],
    resultProfiles: [],
    searching: true,
    noresult: false
  }),
  computed: {
    keywords: () => this.query.replace(/[+]/g, ' ')
  },
  methods: {
    search () {
      new Promise(() => {
        searchBy('profile')
        searchBy('desc')
        searchBy('tag')
        searchBy('comment')
      }).then(() => {
        if (this.resultProfiles.length + this.resultPosts.length == 0) this.noresult = true
        this.searching = false
      })
    },
    searchBy (searchType) {
      this.$http.post(apiRoot + 'search/', {
        query: this.query,
        field: searchType
      }).then(
        (response) => {
          if (searchType === 'profile')
              response.data.data.result.forEach(item => this.resultProfiles.push(item))
          else
              response.data.data.result.forEach(item => this.resultPosts.push(item))
        },
        (response) => {
          switch (response.status) {
            case 400:
              console.error('ERR: search without query')
              break;
            case 404:
              console.error('ERR: search no result')
              break;
          }
        })
    }
  }
}
</script>

<style lang="css">
#search-loader {
  margin: 0 auto;
}
</style>
