<!DOCTYPE html>
<html>
	<head>
		<title>Connectez-vous</title>
		<meta charset="utf-8">
	</head>

	<body>

		<h1>Se connecter</h1>
		<form method="POST" action="../controllers/login.php">
			<input type="email" name="user_email" placeholder="Email" required>
			<input type="password" name="user_passwd" placeholder="Mot de passe" required>
			<input type="submit" value="Connexion">
		</form>

		<?php if(isset($log)): ?>
		<h5><?php echo $log; ?></h5>
		<?php endif; ?>

	</body>
</html>