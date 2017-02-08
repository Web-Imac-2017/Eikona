<!DOCTYPE html>
<html>
	<head>
		<title>Page d'activations</title>
		<meta charset="utf-8">
	</head>

	<body>

		<h1>Page d'activation</h1>
		<form method="POST">
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" required>
			<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>" required>
			<input type="submit" value="ACTIVER">
		</form>

		<h4><?php echo $log; ?></h4>

	</body>
</html>