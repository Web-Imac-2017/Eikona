<?php

$content = "

<!DOCTYPE html>
<html>
	<head>
		<title>Récupérez votre mot de passe</title>
		<meta charset='UTF-8'>
	</head>

	<body>

		<p>Pour définir un nouveau mot de passe, vous aurez besoin de ce code : </p>
		<h1>".sha1($time)."</h1>
	</body>

</html>";