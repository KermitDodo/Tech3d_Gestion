<?php 
	session_start();

	if(!isset($_SESSION['LOGGED_USER'])):
	header('Location: index.php');
	exit;
	endif; 

	// Afficher les erreurs
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();

	// Verifier si le formulaire a été retourné
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Récupérer les données du formulaire
		$type = $_POST['type'];
		$numAffaire = $_POST['num_affaire'];
		$intituleAffaire = $_POST['intitule_affaire'];
		$client = $_POST['client'];

		if(!empty($type) && !empty($numAffaire) && !empty($intituleAffaire)) {	

			// Ecrire la requête
			$sqlQuery = 'INSERT INTO affaires(type_affaire, num_affaire, intitule, id_client) VALUES (:type_affaire, :num_affaire, :intitule, :id_client)';
			$insertAffaire = $db->prepare($sqlQuery);

			// Executer la requete
			$insertAffaire->execute([
				'type_affaire' => $type,
				'num_affaire' => $numAffaire,
				'intitule' => $intituleAffaire,
				'id_client' => $client,
			]);	
		}
	}?>

<!DOCTYPE html>
<html>
<head>
	<title>Interface de gestion</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
				<h4 class="titre">Interface de gestion</h4>
			</div>
			<!-- Inserer formulaire -->
			<form method="post">
				<div class="mb-3">
					<fieldset>
						<legend class="form-label">Choisir une catégorie :</legend>
						<input type="radio" name="type" value="Affaire" id="Affaires" style="display: inline-block; width: 30px;" checked>
						<label for="Affaires" class="form-label" style="display: inline-block; margin-right: 10px;">Affaires</label>
						<input type="radio" name="type" value="Devis" id="Devis" style="display: inline-block; width: 30px;">
						<label for="Devis" class="form-label" style="display: inline-block; margin-right: 10px;">Devis</label>
						<input type="radio" name="type" value="Divers" id="Divers" style="display: inline-block; width: 30px;">
						<label for="Divers" class="form-label" style="display: inline-block;">Divers</label>
					</fieldset>
					<div class="mb-3">
						<label for="num_affaire" class="form-label">N° Affaire<span class="requis">*</span> :</label><br>
						<input name="num_affaire" id="num_affaire" required>
					</div>
					<div class="mb-3">
						<label for="intitule_affaire" class="form-label">Intiulé Affaire<span class="requis">*</span> :</label><br>
						<input name="intitule_affaire" id="intitule_affaire" required>
					</div>
				</div>
				<div class="mb-3">
					<label for="client" class="form-label">Client :</label><br>
					<select name="client" id="client">
						<?php
							$clients = $db->query('SELECT * FROM clients ORDER BY nom ASC');
							foreach ($clients as $client) {
								echo '<option value="' . $client['id_client'] . '">' . $client['nom'] .'</option>';
							}
						?>
					</select>
				</div>
				<button type="submit" class="btn btn-primary">Envoyer</button>
				<p class="requis" style="margin-top: 5px"><em>Les champs suivis d'un * doivent étre complétés.</em></p>
			</form>
		</div>
	</main>
	
	<footer>
		<div class="container">
			<?php include_once('footer.php'); ?>
		</div>
	</footer>
<?php
	/*if(($_SESSION['LOGGED_USER']['idEmploye']) == 1): {
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
	} endif;*/
?>				
</body>
</html>