<template>
	<md-layout md-flex="50" md-flex-offset="5">

		<md-whiteframe id="main-section" md-elevation="8" md-tag="section">

			<md-dialog md-open-from="#fab" md-close-to="#fab" ref="dialog_add" class="dialog_add">
				<md-layout md-flex="66" md-column>
					<md-dialog-title>Create new post</md-dialog-title>
					<md-dialog-content>
				    	<addPost></addPost>
					</md-dialog-content>
				</md-layout>
			</md-dialog>

			<md-dialog md-open-from="#fab" md-close-to="#fab" ref="dialog_modif" class="dialog1">
				<md-layout>
					<md-dialog-content>
						<addPost></addPost>  <!-- A changer par component de modification -->
					</md-dialog-content>
				</md-layout>
			</md-dialog>

			<div class="add_button">
				<md-button class="md-fab" id="fab" @click.native="openDialog('dialog_add')">
				  <md-icon>add</md-icon>
				</md-button>
			</div>

			<div v-for="image in images" :class="classStatut">
				<md-button class="modif_button" id="fab" @click.native="openDialog('dialog_modif')"></md-button>
				<img :src="image.src" alt="image non chargee"/>
			</div>
		</md-whiteframe>

	</md-layout>
</template>

<script type="text/javascript">
import addPost from './addPost.vue'

export default{

	name: 'previewsPosts',
	components: {
		addPost
	},
	data () {
		return {
			images: [{src: './../../assets/Cameleon/c1.jpg', modif:true}, {src: './../../assets/Cameleon/c2.jpg', modif:false}, {src: './../../assets/Cameleon/c3.jpg', modif:false}, {src: './../../assets/Cameleon/c4.jpg', modif:true}, {src: './../../assets/Cameleon/c5.jpg', modif:true}, {src: './../../assets/Cameleon/c6.jpg', modif:false}, {src: './../../assets/Cameleon/c7.jpg', modif:true}, {src: './../../assets/Cameleon/c8.jpg', modif:true}],
			follower: true,
			confirm: {
		      title: 'Suppression d\'une publication',
		      contentHtml: 'Etes-vous vraiment sûres de vouloir supprimer cette publication. Cela entraînera la perte des données la concernant.',
		      ok: 'Supprimer',
		      cancel: 'Annuler'
		    }
		}
	},
	computed: {
		classStatut () {
			return "square_image";
		}
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

.dialog_add{
	width: 100%;
	height: 100%;
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

.square_image, .add_button, .square-image__modif, .modif_button{
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

.square_image__modif{
	opacity: 0.75;
}

.add_button{
	justify-content: center;
}

.add_button md-button{
	vertical-align: middle;
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

    cursor: pointer;
}

.modif_button{
	position: absolute;
	z-index: 1;
	margin:0;
}

</style>