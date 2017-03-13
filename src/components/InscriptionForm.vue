<template>
	<form id="inscriptionForm" @submit.stop.prevent="register">
		<p v-if="error_message != ''" class='md-warn'>{{ error_message }}</p>
		<md-input-container id="inscription-mail">
			<label>Email</label>
			<md-input required type="email" v-model="email"></md-input>
			<span class="md-error">Adresse email non valide ou déjà utilisée.</span>
		</md-input-container>
		<md-input-container id="inscription-name">
			<label>Nom d'utilisateur</label>
			<md-input required v-model="name"></md-input>
			<span class="md-error">Nom invalide (seulement lettres, chiffres, _, -, 3 caractères minimum)</span>
		</md-input-container>
		<md-input-container md-has-password id="inscription-password">
			<label>Mot de passe</label>
			<md-input required type="password" v-model="password"></md-input>
			<span class="md-error">Mot de passe invalide (8 caractères minimum)</span>
		</md-input-container>
		<md-input-container md-has-password id="inscription-confirm">
			<label>Confirmation mot de passe</label>
			<md-input required type="password" v-model="confirmation"></md-input>
			<span class="md-error">Les mots de passes sont différents.</span>
		</md-input-container>
		<p>En m'inscrivant j'accepte les <a href="CGU.html">CGU</a> et la <a href="confidentialite.html">Politique de confidentialité</a></p>
		<p>Les champs marqués d'un * sont obligatoires.</p>
		<md-button class="md-primary md-raised" type="submit">JE M'INSCRIS</md-button>
		<md-dialog-alert
				:md-title="alert.title"
				:md-content-html="alert.contentHtml"
  			ref="mailConfirmation">
		</md-dialog-alert>
	</form>
</template>


<script>
import apiRoot from './../config.js'
import formVerifications from './../formVerifications.js'

export default {
	name: 'inscriptionForm',
	data () {
		return {
			email: '',
			name: '',
			password: '',
			confirmation: '',
			alert: {
	      title: 'Inscription réussie',
	      contentHtml: 'Un mail d\'activation vient de vous être envoyé.'
	    },
			error_message: ''
		}
	},
	mixins: [formVerifications],
	methods: {
		register () {
			console.log('register', {
				user_name: this.name,
				user_email: this.email,
				user_passwd: this.password,
				user_passwd_confirm: this.confirmation
      })
			// vérifer validitée des champs
			if (!(this.verif_mail(this.email, 'inscription-mail') &&
						this.verif_name(this.name, 'inscription-name') &&
						this.verif_password(this.password, 'inscription-password') &&
						this.verif_confirm(this.password, this.confirmation, 'inscription-confirm'))) return
			this.banned_mail(this.email).catch(() => {this.email = ' -Email Banni- ' + this.email})
			this.banned_word(this.name).catch(() => {this.name = ' -Mot Banni- ' + this.name})
			this.$http.post(apiRoot + 'auth/register/', {
				user_name: this.name,
				user_email: this.email,
				user_passwd: this.password,
				user_passwd_confirm: this.confirmation
      }).then(
			(response) => {
        console.log('Sign up success', response)
				this.$refs['mailConfirmation'].open()
      }, (response) => {
        console.log('Sign up error', response)
        switch (response.status) {
          case 400:
            console.log('Bad request')
						this.error_message = 'Problème de connexion. Veuillez essayer plus tard.'
            break
          case 403:
            console.log('Forbidden')
						document.getElementById('inscription-mail').classList.add('md-input-invalid')
            break
          case 409:
            console.log('Conflict')
						document.getElementById('inscription-confirm').classList.add('md-input-invalid')
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
}
</style>
