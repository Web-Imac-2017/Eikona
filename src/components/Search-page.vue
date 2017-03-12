<template lang="html">
  <md-whiteframe>
    <md-spinner id="search-loader" v-if="searching" md-indeterminate></md-spinner>
    <md-layout v-else>
      <md-layout v-if="noresult">
        <p>Nous n'avons trouvé aucun résultat correspondant à la recherche</p>
        <p>{{keywords}}</p>
      </md-layout>
      <md-layout v-else>
        <md-list v-if="resultProfiles.length > 0">
          <md-subheader>Profils</md-subheader>
          <profile v-for="profile in resultProfiles" :profile="profile" :index="-1" :extended="false" @select="profileSelect"></profile>
        </md-list>
        <md-whiteframe v-if="resultPosts.length > 0">
          <md-subheader>Publications</md-subheader>
          <post v-for="post in resultPosts" :post="post"></post>
        </md-whiteframe>
      </md-layout>
    </md-layout>
  </md-whiteframe>
</template>

<script>
import post from './Post.vue'
import profile from './Profile.vue'
import apiRoot from './../config.js'

export default {
  name: 'searchPage',
  components: {
    post,
    profile
  },
  props: ['query'],
  data: () => ({
    resultPosts: [],
    resultProfiles: [],
    searching: true
  }),
  computed: {
    keywords () { return this.query.replace(/[+]/g, ' ') },
    noresult () {
        if (this.resultProfiles.length + this.resultPosts.length == 0) return true
        else return false
    }
  },
  mounted () {
    this.search()
  },
  watch: {
    query: 'search'
  },
  methods: {
    search () {
      this.resultPosts = []
      this.resultProfiles = []
      this.searchBy(this.query, 'profile')
      this.searchBy(this.query, 'description')
      this.searchBy(this.query, 'tag')
      this.searchBy(this.query, 'comment')
      this.searching = false
    },
    searchBy (query, searchType) {
      this.$http.post(apiRoot + 'search/', {
        query: query,
        field: searchType
      }).then(
        (response) => {
          console.log('searchby : ' + searchType, response)
          if (searchType === 'profile')
              response.data.data.result.forEach(item => this.resultProfiles.push(item))
          else
              response.data.data.result.forEach(item => this.resultPosts.push(item))
        },
        (response) => {
          switch (response.status) {
            case 400:
              console.error('ERR: search by ' + searchType + ' without query')
              break;
            case 404:
              console.log('Search by ' + searchType + ' no result')
              break;
          }
        })
    },
    profileSelect () {
      // redirect vers la page du profil
      this.$router.push('')
    }
  }
}
</script>

<style lang="css">
#search-loader {
  margin: 0 auto;
}
</style>
