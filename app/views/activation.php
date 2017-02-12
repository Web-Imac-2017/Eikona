<!DOCTYPE html>
<html>
	<head>
		<title>Page d'activation</title>
		<meta charset="utf-8">
	</head>

	<body>

		<h1>Page d'activation</h1>

		<?php if(isset($_GET['id'])): ?>
		<form method="POST" action="../controllers/activation.php">
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>">
			<input type="submit" value="ACTIVER">
		</form>
		<?php endif; ?>

		<?php if(isset($log)): ?>
		<h4><?php echo $log; ?></h4> 
		<?php endif; ?>

	</body>
</html>