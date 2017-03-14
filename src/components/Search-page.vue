<template lang="html">
  <md-whiteframe>
    <md-spinner id="search-loader" v-if="searching" md-indeterminate></md-spinner>
    <md-layout v-else>
      <md-layout v-if="noresult">
        <p>Nous n'avons trouvé aucun résultat correspondant à la recherche suivante :</p><br/>
        <p>{{keywords}}</p>
      </md-layout>
      <md-layout v-else>
        <md-list v-if="resultProfiles.length > 0">
          <md-subheader>Profils</md-subheader>
          <profile v-for="profile in resultProfiles" :profile="getProfileFormat(profile)" :index="-1" :extended="false" @select="profileSelect"></profile>
        </md-list>
        <md-whiteframe v-if="resultPosts.length > 0">
          <md-subheader>Publications</md-subheader>
          <post v-for="post in resultPosts" :post="getPostFormat(post)" :profilePost="getProfileFormat(post)"></post>
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
  props: ['type', 'query'],
  data () {
    return {
      resultPosts: [],
      resultProfiles: [],
      searching: true
    }
  },
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
    type: 'search',
    query: 'search'
  },
  methods: {
    getProfileFormat (item) {
      return {
        profileID: item.profile_id,
        profilePict: item.profile_picture,
        profileName: item.profile_name
      }
    },
    getPostFormat (item) {
      return {
        postID: item.post_id,
        originalPicture: null,
        desc: item.description,
        allowComments: item.post_allow_comments
      }
    },
    search () {
      this.resultPosts = []
      this.resultProfiles = []
      if (this.type === 'all') this.searchAll(this.keywords)
      else this.searchBy(this.keywords, this.type)
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
            default:
          }
        })
    },
    searchAll (query) {
      this.$http.post(apiRoot + 'search/', {
        query: query
      }).then(
        (response) => {
          if (response.data.data.profiles !== null) response.data.data.profiles.forEach(item => this.resultProfiles.push(item))
          if (response.data.data.posts !== null) response.data.data.posts.forEach(item => this.resultPosts.push(item))
          if (response.data.data.tags !== null) response.data.data.tags.forEach(item => this.resultPosts.push(item))
          if (response.data.data.comments !== null) response.data.data.comments.forEach(item => this.resultPosts.push(item))
          console.log('searchAll : ', response)
          console.log('Search results : ', this.resultProfiles, this.resultPosts)
        },
        (response) => {
          switch (response.status) {
            case 400:
              console.error('ERR: search in all without query')
              break;
            case 404:
              console.log('Search in all : no result')
              break;
            default:
          }
        })
    },
    profileSelect (id) {
      // redirect vers la page du profil
      this.$router.push('/p/' + id)
    }
  }
}
</script>

<style lang="css">
#search-loader {
  margin: 0 auto;
}
</style>
