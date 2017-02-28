<template>
	<form id="inscriptionForm">
		<div id="emailForm">
			<label>E-mail</label>
			<input type="email" name="user_email" >
		</div>
		<div id="nameForm">
			<label>Nom d'utilisateur</label>
			<input type="text" name="user_ame" >
		</div>
		<div id="pwdForm">
			<label>Mot de passe</label>
			<input type="password" name="user_passwd">
		</div>
		<div id="pwdConfirmForm">
			<label>Confirmation</label>
			<input type="password" name="user_passwd_confirm" label="Confirmation">
		</div>
		<p id="validation">En m'inscrivant j'accepte les <a href="CGU.html">CGU</a> et la <a href="confidentialite.html">Politique de confidentialité</a></p>
		<button  value="JE M'INSCRIS">
	</form>

</template>


<script>

export default {
	name : "inscriptionForm"

	
	
}
</script>

<style scoped>
#inscriptionForm * :not(a){
	display: block;
	
}
a{
	text-decoration: none;
}
input{	
	border: none;
	border-bottom: 1px solid lightgrey;

}
</style>