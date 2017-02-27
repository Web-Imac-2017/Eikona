<?php

$content = "

<!DOCTYPE html>
<html>
	<head>
		<title>Activez votre compte</title>
		<meta charset='UTF-8'>
	</head>

	<body>

		<h2>Pour activer votre compte, veuillez appuyer sur le bouton ci-dessous</h2>
		<form method='POST' action='localhost/Eikona/do/auth/activate'>
			<input type='hidden' value='".$id."' name='user_id'>
			<input type='hidden' value='".sha1($time)."' name='user_key'>
			<input type='submit' value='ACTIVER'>
		</form>
		
		<h3>Si le formulaire ne s'affiche pas correctement, <a href='localhost/Eikona/do/auth/activate?user_id=".$id."&user_key=".sha1($time)."'>veuillez suivre ce lien.</a>
	</body>

</html>";