<template>
	<div id="footer">
		<a href="mentionsLegales.html">Mentions légales</a>
		<a href="confidentialite.html">Confidentialité</a>
		<a href="contact.html">Contactez nous</a>
	</div>
</template>

<script>


export default {
	name: "mainFooter"
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css?family=Lato');
#footer{
	text-align: center;
	width: 100%;	
}

#footer a{
	font-family : 'Lato', sans-serif;
	display: inline-block;
	color : #028090;
	text-decoration: none;
	font-size : 10pt;
	margin: 5px;
	margin-right: 30px;
}
</style>