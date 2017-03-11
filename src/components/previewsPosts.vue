<template>
	<md-layout md-flex="50" md-flex-offset="5">
		<md-whiteframe md-elevation="8" md-tag="section">
			<md-layout :md-gutter="8">
				<md-card>
					<md-card-cover>
						<md-card-media md-ratio="1:1" v-for="image in images">
							<img :src="image" alt="image non chargee"/>
						</md-card-media>
					</md-card-cover>
				</md-card>
			</md-layout>
		</md-whiteframe>
	</md-layout>
</template>

<script type="text/javascript">
export default{
	name: 'previewsPosts',
	data () {
		return {
			images: ['./../../assets/Cameleon/c1.jpg', './../../assets/Cameleon/c2.jpg', './../../assets/Cameleon/c3.jpg', './../../assets/Cameleon/c4.jpg', './../../assets/Cameleon/c5.jpg', './../../assets/Cameleon/c6.jpg', './../../assets/Cameleon/c7.jpg']
		}
	}
}
	
</script>

<style type="text/css" scoped>

.square_image img{
	display: block;
	margin: auto;
	vertical-align: middle;
	min-width: 100%;
	min-height: 100%;
	-ms-interpolation-mode: bicubic;
}

</style>