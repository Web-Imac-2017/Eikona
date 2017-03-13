import apiRoot from './config.js'

export default {
  methods: {
    banned_mail (mail) {
      if (mail === null) return
      return Vue.http.post(apiRoot + '/ban/is/mail/', {email: mail}).then(response => {
        console.log('ban mail : ', response)
        resolve()
      }, response => {
        console.error('ban mail : ', response)
        reject()
      })
    },
    banned_word (word) {
      if (word === null) return
      return Vue.http.post(apiRoot + '/ban/is/word/', {word: word}).then(response => {
        console.log('ban word : ', response)
        resolve()
      }, response => {
        console.error('ban mail : ', response)
        reject()
      })
    },
    verif_mail (value, inputContainerId) {
			  var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			  if(!regex.test(value)){
					document.getElementById(inputContainerId).classList.add('md-input-invalid')
					return false
				} else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
				return true
		},
		verif_name (value, inputContainerId) {
			// Autorise les chiffres, lettres, -, _ avec un minimum de 3 caractères
			var regex = /^[a-zA-Z0-9_-]{3,}$/
			if(!regex.test(value)){
				document.getElementById(inputContainerId).classList.add('md-input-invalid')
				return false
			} else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
			return true
		},
		verif_password (value, inputContainerId) {
			// Autorise les chiffres, lettres, -, _ avec un minimum de 8 caractères
			var regex = /^[a-zA-Z0-9_-]{8,}$/
			if(!regex.test(value)){
				document.getElementById(inputContainerId).classList.add('md-input-invalid')
				return false
			} else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
			return true
		},
		verif_confirm (value, confirmation, inputContainerId) {
			if (value != confirmation){
				document.getElementById(inputContainerId).classList.add('md-input-invalid')
				return false
			} else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
			return true
		},
    verif_text(value, inputContainerId){
      var regex = /[\ta-zA-Z0-9 -._//:();^/@?!]+/
			if(!regex.test(value)){
				document.getElementById(inputContainerId).classList.add('md-input-invalid')
				return false
			} else document.getElementById(inputContainerId).classList.remove('md-input-invalid')
			return true
    }
  }
}
