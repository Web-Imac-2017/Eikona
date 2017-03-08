<template>
	<form id="inscriptionForm" @submit.stop.prevent="register">
		<md-input-container>
			<label>Email</label>
			<md-input required type="email" v-model="email"></md-input>
		</md-input-container>
		<md-input-container>
			<label>Nom d'utilisateur</label>
			<md-input required v-model="name"></md-input>
		</md-input-container>
		<md-input-container md-has-password>
			<label>Mot de passe</label>
			<md-input required type="password" v-model="password"></md-input>
		</md-input-container>
		<md-input-container md-has-password>
			<label>Confirmation mot de passe</label>
			<md-input required type="password" v-model="confirmation"></md-input>
		</md-input-container>
		<p id="validation">En m'inscrivant j'accepte les <a href="CGU.html">CGU</a> et la <a href="confidentialite.html">Politique de confidentialité</a></p>
		<p>Les champs marqués d'un * sont obligatoires.</p>
		<md-button class="md-primary md-raised" type="submit">JE M'INSCRIS</md-button>
	</form>

</template>


<script>

export default {
	name : "inscriptionForm",
	data () {
		return {
			email: "",
			name: "",
			password: "",
			confirmation: ""
		}
	},
	methods :{
		register () {
			console.log("register")
			// vérifer validitée des champs
      this.$http.post('/do/auth/register/', {
				user_name : this.name,
				user_email : this.email,
				user_passwd : this.password,
				user_passwd_confirm : this.confirmation
      }).then((response) => {
        console.log('Sign up success', response)
				//Notifier l'envoi du mail
      }, (response) => {
        console.log('Sign up error', response)
        switch(response.code){
          case 400:
            console.log('Bad request')
            break
          case 401:
            console.log('Unauthorized')
            break
          case 404:
            console.log('Not found')
            break
          case 409:
            console.log('Conflict')
            break
        }
      })
		}
	}



}
</script>

<style scoped>
a{
	text-decoration: none;
}
p {
  font-size: x-small;
  color: darkgray;
  text-align: center;
}
</style>
