<!DOCTYPE html>
<html>
	<head>
		<title>Inscription</title>
		<meta charset="utf-8">
	</head>

	<body>

		<h1>Les champs pour s'inscrire</h1>
		<form method="POST">
			<input type="text" name="user_name" placeholder="Prénom" required autocomplete>
			<input type="email" name="user_email" placeholder="email" required>
			<input type="password" name="user_passwd" placeholder="Mot de passe" required>
			<input type="password" name="user_passwd_confirm" placeholder="Retaper le mot de passe" required>
			<input type="submit" value="S'inscrire">
		</form>

	</body>
</html>