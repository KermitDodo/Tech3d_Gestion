<?php 
	// Ouverture de la session
	if (!isset($_SESSION)) {
		session_start();
	}

	if (!isset($_SESSION['LOGGED_USER'])) {
		header('Location: index.php');
		exit;
	}

	// Afficher les erreurs
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

    include_once('queries.php');

	// Récupérer infos employé
	$employes = $employes["lireUn"]($_SESSION['LOGGED_USER']['idEmploye']); 

	// Verifier si le formulaire a été retourné
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		// Récupérer les données du formulaire
		$nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$naissance = $_POST['naissance'];
		$telephone = $_POST['telephone'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$passwordConfirm = $_POST['passwordConfirm'];
		
		// Preparation de la requete
		$sqlQuery = 'UPDATE employes SET nom=:nom, prenom=:prenom, naissance=:naissance, telephone=:telephone, email=:email WHERE id_employe=:id';
		$modifPointage = $db->prepare($sqlQuery);

		// Executer la requete
		$modifPointage->execute([
			'nom' => $nom,
			'prenom' => $prenom,
			'naissance' => $naissance,
			'telephone' => $telephone,
			'email' => $email,
			'id' => $_SESSION['LOGGED_USER']['idEmploye']
		]);
			
		// Modification du mot de passe
		if ($password === $passwordConfirm) {
			$passwordHash = md5($password);
			$sqlQuery = 'UPDATE employes SET password=:password WHERE id_employe=:id';
			$modifPointage = $db->prepare($sqlQuery);
			$modifPointage->execute([
				'password' => $passwordHash,	
				'id' => $_SESSION['LOGGED_USER']['idEmploye']
			]);
			
			// Unlog utilisateur si mdp changé
			include('logout.php');
			exit; 	
		}
		
	}?>

<!DOCTYPE html>
<html>
<head>
	<title>Interface de gestion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
	<header>
		<div class="container">
			<?php include_once('header.php'); ?>
		</div>	
	</header>
	
	<main title="principale">
		<div class="container">
			<div>
				<h4 class="titre">Paramètres utilisateur</h4>
			</div>
			<!-- Inserer formulaire -->
			<form method="post">
				<div class="mb-3">
					<div class="mb-3">
						<label for="nom" class="form-label">Nom :</label><br>
						<input name="nom" id="nom" type="text" value="<?php echo $employes['nom'] ?>">
					</div>
					<div class="mb-3">
						<label for="prenom" class="form-label">Prénom :</label><br>
						<input name="prenom" id="prenom" type="text" value="<?php echo $employes['prenom'] ?>">
					</div>
					<div class="mb-3">
						<label for="naissance" class="form-label">Date de naissance :</label><br>
						<input name="naissance" id="naissance" type="date" value="<?php echo $employes['naissance'] ?>">
					</div>
					<div class="mb-3">
						<label for="telephone" class="form-label">Téléphone :</label><br>
						<input name="telephone" id="telephone" type="tel" value="<?php echo $employes['telephone'] ?>">
					</div>					
					<div class="mb-3">
						<label for="email" class="form-label">E-Mail :</label><br>
						<input name="email" id="email" type="email" value="<?php echo $employes['email'] ?>" required>
					</div>								
					<div class="mb-3">
						<label for="password" class="form-label">Mot de Passe :</label><br>
						<input name="password" id="password" type="password">
					</div>								
					<div class="mb-3">
						<label for="passwordConfirm" class="form-label">Confirmer mot de Passe :</label><br>
						<input name="passwordConfirm" id="passwordConfirm" type="password">
					</div>
				<button type="submit" class="btn btn-primary">Valider</button>
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