<template>
	<md-layout id="main-layout-add" md-flex="33" md-flex-offset="33" md-column>
		<form md-column id="form" @submit.stop.prevent="create">
			<p v-if="error_message != ''" class='md-warn'>{{ error_message }}</p>
			<md-layout id="Image-input" md-flex="66" md-column>
				<md-layout class="Image-input__image-wrapper">
			      <i v-show="!imageSrc" class="box"></i>
			      <img v-show="imageSrc" class="Image-input__image" :src="imageSrc">
	    		</md-layout>
			</md-layout>

			<md-layout class="Image-input__input-wrapper">
			    Choisir une image
			    <input @change="previewThumbnail" class="Image-input__input" name="thumbnail" type="file"/>
			</md-layout>

			<md-layout md-flex="66">
				<md-input-container>
		  			<label>Description</label>
		  			<md-textarea v-model="description"></md-textarea>
				</md-input-container>

				<md-layout md-align="end">
					<md-button class="md-raised md-primary" type="submit">Créer</md-button>
					<md-button class="md-raised">Annuler</md-button>
				</md-layout>
			</md-layout>
		</form>
	</md-layout>
</template>

<script>
	import Vuex from 'vuex'
	import store from './connectionStore.js'
	import apiRoot from './../config.js'
	import formVerifications from './../formVerification.js'
	export default{
		name: 'addPost',
		store: store,
		data () {
			return {
				no_image: true,
				image: null,
				up: false,
				description: '',
				imageSrc: '',
				error_message: ''
			}
		},
		computed: {
		    ...Vuex.mapGetters([
		      'getUser',
		      'currentProfile'
		    ])
		  },
		methods: {
			create () {
				return
			      this.$http.post(apiRoot + 'post/create', {
			        img: this.imageSrc,
			        postDescription: this.description
			      }).then((response) => {
			      	console.log('Create success', response);

			        /*this.$router.push('/Eikona/user/profile')*/
			      }, (response) => {
			        this.clearUserStore()
			        switch (response.status) {
			          case 201:
			            console.log('Post created')
			            break
			          case 400:
			            this.error_message = 'Une erreur est survenu dans le fichier:fichier manquant/erreur à l\'upload.'
			            break
			          case 401:
			            this.error_message = 'L\'utilisateur n\'est pas connecté'
			            break
			          case 415:
			            this.error_message = 'Le fichier n\'est pas une image png/jpeg/bmp'
			            break
			          default:
			            console.log('Unknown error')
        			}
				})
			},
			previewThumbnail(event) {
            var input = event.target;

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = function(e) {
                    vm.imageSrc = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
		}
	}
}
</script>

<style scoped>

.box {
	opacity: 0.5;
    background-image: url("./../../assets/insert.svg");
    background-size: 35%;
    background-repeat: no-repeat;
    background-position: center;
    width: 100%;
    height: 25vw;
}

#main-layout-add{
	padding: 15px;
	border: 1px solid black;
}

.Image-input {
    display: flex;
}

.Image-input__image-wrapper {
    border-radius: 1px;
    margin-bottom: 10px;
    border-radius: 1px;
    background: #eee;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.Image-input__image {
    max-width: 100%;
    border-radius: 1px;
}

.Image-input__input-wrapper {
    overflow: hidden;
    position: relative;
    background: #eee;
    border-radius: 1px;
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    color: rgba(0,0,0,0.2);
    transition: 0.4s background;
    width: 100%;
    height: 50px;
}

.Image-input__input-wrapper:hover {
    background: #e0e0e0;
}


.Image-input__input {
    cursor: inherit;
    display: block;
    font-family: 'Roboto';
    font-size: 0.8em;
    font-weight: 300;
    width: 100%;
    height: 100%;
    opacity: 0;
    position: absolute;
    right: 0;
    text-align: right;
    top: 0;
    cursor: pointer;
}

p {
  font-size: x-small;
  color: darkgray;
}
	
</style>