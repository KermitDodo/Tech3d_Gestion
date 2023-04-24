<?php
session_start();

if (isset($_POST['email']) &&  isset($_POST['password'])) {

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();
	
	$employes = $db->query('SELECT * FROM employes');
	foreach ($employes as $employe) {
		if (
			$employe['email'] == $_POST['email'] &&
			$employe['password'] == md5($_POST['password']) &&
			$employe['actif'] == 1
		) {
			$_SESSION['LOGGED_USER'] = [			
				'idEmploye' => $employe['id_employe'],
				'email' => $employe['email'],
				'nom' => $employe['nom'],
				'prenom' => $employe['prenom'],
				'grade' => $employe['grade']
			];
			header("Location: form_pointage.php");
			exit();
		} else {
			$errorMessage = sprintf('Les informations saisies ne permettent pas de vous identifier : (%s/%s)',
				$_POST['email'],
				$_POST['password']
			);
		}
	}

}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech 3D Pointage</title>
	<link rel="icon" type="image/png" href="img/favicon_tech3d.png">
    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex flex-column min-vh-100">
	<header>
		<div class="container">
			<?php include_once('header.php'); ?>
		</div>	
	</header>
	
	<main title="principale">
		<div class="container">
			<div>
				<h1 class="titre">Bonjour !</h1>
			</div>
			<!-- Inserer formulaire -->
			<form method="post">
				<div class="mb-3">			
					<!-- si message d'erreur on l'affiche -->
					<?php if(isset($errorMessage)) : ?>
						<div class="alert alert-danger" role="alert">
							<?php echo $errorMessage; ?>
						</div>
					<?php endif; ?>
					<div class="mb-3">
						<label for="email" class="form-label">Email</label>
						<input type="email" class="form-control" id="email" name="email" aria-describedby="email-help" placeholder="you@exemple.com">
						<div id="email-help" class="form-text">Email fourni par votre administrateur.</div>
					</div>
					<div class="mb-3">
						<label for="password" class="form-label">Mot de passe</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<button type="submit" class="btn btn-primary">Envoyer</button>
				</div>
			</form>
		</div>
	</main>

	<footer>
		<div class="container">
			<?php include_once('footer.php'); ?>
		</div>
	</footer>	
</body>
</html>	