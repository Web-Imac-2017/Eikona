<template>
	<md-layout md-flex="50" md-flex-offset="5">

		<md-whiteframe v-if="!follower" id="main-section" md-elevation="8" md-tag="section">
			<div class="private-profile">
				<P class="bold">Ce profil est privé</p>
				<p class="thin">Abonnez-vous afin d'en voir le contenu !</P>
			</div>
		</md-whiteframe>

		<md-whiteframe v-else id="main-section" md-elevation="8" md-tag="section">
			<md-dialog md-open-from="#fab" md-close-to="#fab" ref="dialog_see" class="dialog1">
				<md-layout md-flex="66" md-column>
					<md-dialog-content>
				    	<Post></Post>
					</md-dialog-content>
				</md-layout>
			</md-dialog>

			<div v-for="image in images" class="square_image">
				<md-button class="see_post" @click.native="openDialog('dialog_see')"></md-button>
				<img :src="image" alt="image non chargee"/>
			</div>
		</md-whiteframe>

	</md-layout>
</template>

<script type="text/javascript">
import Post from './Post.vue'

export default{
	name: 'previewsPosts',
	components:{
		Post
	},
	data () {
		return {
			images: ['./../../assets/Cameleon/c1.jpg', './../../assets/Cameleon/c2.jpg', './../../assets/Cameleon/c3.jpg', './../../assets/Cameleon/c4.jpg', './../../assets/Cameleon/c5.jpg', './../../assets/Cameleon/c6.jpg', './../../assets/Cameleon/c7.jpg', './../../assets/Cameleon/c8.jpg'],
			currentProfile: false,
			follower: true
		}
	},
	computed: {
	},
	methods: {
		openDialog(ref) {
	      this.$refs[ref].open();
	    },
	    closeDialog(ref) {
	      this.$refs[ref].close();

	    },
	    onOpen() {
	      console.log('Opened');
	    },
	    onClose(type) {
	      console.log('Closed', type);
	    }
	}
}
	
</script>

<style type="text/css" scoped>

div.private-profile {
	width: 25%;
	margin: auto;
	text-align: center;
}

div.private-profile p {
	font-family: 'Roboto';
	font-size: 1em;
}

p.bold{
	font-weight: 500;
}

p.thin{
	font-weight: 100;
}

#main-section{
	width: 100%;
	min-height: 50%;
	padding: 10px 0 0 10px;
	display: -webkit-box;
	display: -moz-box;
	display: -ms-flexbox;
	display: -webkit-flex;
	display: flex;

	-webkit-flex-flow: row wrap;
	flex-flow: row wrap;
}

.square_image, .add_button, .square-image__modif, .see_post{
	display: flex;
	width: 150px;
	height: 150px;
	max-width:150px;
	max-height: 150px;

	margin-right: 10px;
	margin-bottom: 10px;

	flex-basis: 150px;
	overflow: hidden;
}

.square_image img{
	display:inline-block;
	min-width: 100%;
	min-height: 100%;
	-ms-interpolation-mode: bicubic;
}

.square_image:hover img{

	filter: brightness(75%);
    -webkit-filter: brightness(75%);
    -moz-filter: brightness(75%);
    -o-filter: brightness(75%);
    -ms-filter: brightness(75%);
}

.see_post{
	position: absolute;
	z-index: 3;
	margin:0;
}

</style>