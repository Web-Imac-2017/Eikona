<template>
  <div id="app">
    <topHeader></topHeader>
    <post :post="this.post"></post>
    <ModificationPost :post="this.post"></ModificationPost>
    <router-view></router-view>
    <mainFooter></mainFooter>
  </div>
</template>

<script>
import topHeader from './components/Header.vue'
import mainFooter from './components/MainFooter.vue'
import post from './components/Post.vue'
import ModificationPost from './components/ModificationPost.vue'
export default {
  name: 'app',
  components: {
    topHeader,
    mainFooter,
    post,
    ModificationPost
  },
  data(){    
    return{
      post:{
        post_id : 1,
        post_description :'blablabla the description of the century',
        editedPicture : null,
        originalPicture : '../assets/testPhoto.jpg',
        contactPicture : '../assets/testPhoto.jpg'
      }
    }
  }
}
</script>
