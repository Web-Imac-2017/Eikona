<template>
	<md-layout id="main-layout-add" md-flex="33" md-flex-offset="33" md-column>
		<div id="Image-input">
			<div class="Image-input__image-wrapper">
		      <i v-show="!imageSrc" class="icon fa fa-picture-o"></i>
		      <img v-show="imageSrc" class="Image-input__image" :src="imageSrc">
    		</div>

    		<div class="Image-input__input-wrapper">
			    Choose
			    <input @change="previewThumbnail" class="Image-input__input" name="thumbnail" type="file">
			</div>
		</div>	
		<form md-column id="form" @submit.stop.prevent="post">
			<md-layout md-flex="66">
				<md-input-container>
		  			<label>Description</label>
		  			<md-textarea v-model="description"></md-textarea>
				</md-input-container>

				<md-layout md-align="end">
					<md-button class="md-raised md-primary" type="submit">Publier</md-button>
					<md-button class="md-raised">Annuler</md-button>
				</md-layout>
			</md-layout>
		</form>
	</md-layout>
</template>

<script>
	export default{
		name: 'addPost',
		data () {
			return {
				no_image: true,
				image: null,
				up: false,
				imageSrc: null
			}
		},
		methods: {
			vmod () {
				console.log(this.image);
			},
			post () {

				console.log("kikoo");
			},
			previewThumbnail(event) {

            var input = event.target;
			console.log(input);
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

/*.box {
	background-color: lightgrey;
	opacity: 0.5;
    background-image: url("../../assets/insert.svg");
    background-size: 35%;
    background-repeat: no-repeat;
    background-position: center;
    width: 100%;
    height: 25vw;
    text-align: center;
    vertical-align: text-bottom;
}

img {
    width: 6em;
}

#main-layout-add{
	padding: 15px;
	border: 1px solid black;
}

#image_placement{
	width: 100%;
	height: 25vw;
	display: flex;
}

md-icon{
	vertical-align: middle;
	margin: auto;
}*/

.Image-input {
	width: 100%;
	height: 25vw;
    display: flex;
}

.Image-input__image-wrapper {
    flex-basis: 80%;
    width: 100%;
    height: 150px;
    flex: 2.5;
    border-radius: 1px;
    margin-right: 10px;
    overflow-y: hidden;
    border-radius: 1px;
    background: #eee;
    justify-content: center;
    align-items: center;
    display: flex;
}

.Image-input__image-wrapper > .icon {
    color: #ccc;
    font-size: 50px;
    cursor: default;
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
    float: left;
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    color: rgba(0,0,0,0.2);
    transition: 0.4s background;
}

.Image-input__input-wrapper:hover {
    background: #e0e0e0;
}


.Image-input__input {
    cursor: inherit;
    display: block;
    font-size: 999px;
    min-height: 100%;
    opacity: 0;
    position: absolute;
    right: 0;
    text-align: right;
    top: 0;
    cursor: pointer;
}
	
</style>