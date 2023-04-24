<?php 
	session_start();

	if(!isset($_SESSION['LOGGED_USER'])):
	header('Location: index.php');
	exit;
	endif; 

	include_once('functions.php');

	// Récupérer l'identifiant
	$id = $_GET['id'];
		
	// Se connecter à la BDD
	$db = bddConnect();	

	// Récupérer les données existantes depuis la base de données
	$requete = $db->prepare('SELECT * FROM suivi_heures WHERE id_suivi_heures = :id');
	$requete->execute(array('id' => $id));
	$resultat = $requete->fetch();

	// Vérifier si le formulaire a été soumis
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		// Récupérer les données du formulaire
		$date = $_POST['date'];
		$heureDebut = $_POST['heure_debut'];
		$heureFin = $_POST['heure_fin'];
		$idAffaire = $_POST['num_affaire'];
		$idTache = $_POST['tache'];
		$description = $_POST['description'];
		$trajet = $_POST['trajet'];
		$distance = $_POST['distance'];

		// Ecrire la requête
		$sqlQuery = 'UPDATE suivi_heures SET date=:date, heure_debut=:heure_debut, heure_fin=:heure_fin, id_affaire=:id_affaire, id_tache=:id_tache, description=:description, trajet=:trajet, nb_km=:distance WHERE id_suivi_heures=:id';
		$modifPointage = $db->prepare($sqlQuery);

		// Executer la requete
		$modifPointage->execute([
			'date' => $_POST['date'],
			'heure_debut' => $_POST['heure_debut'],
			'heure_fin' => $_POST['heure_fin'],
			'id_affaire' => $_POST['num_affaire'],
			'id_tache' => $_POST['tache'],
			'description' => $_POST['description'],
			'trajet' => $_POST['trajet'],
			'distance' => $_POST['distance'],
			'id' => $id
		]);

		// Rediriger vers la page tableau_pointage.php
		include('tableau_pointage.php');
		exit; 
	}
?>
		
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech3D Pointage - Modification</title>
	<link rel="icon" type="image/png" href="img/favicon_tech3d.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="script.js"></script>
</head>
	
<body class="d-flex flex-column min-vh-100">
	<header>
		<div class="container">
			<?php include_once('header.php'); ?>
		</div>	
	</header>
	
	<main title="principale">
		<!-- Inserer formulaire -->
		<div class="container">	
			<div>
				<h4 class="titre">Modification de la saisie</h4>
			</div>
			<form method="post">
				<div class="mb-3">
					<label for="date" class="form-label">Date :</label><br>
					<input type="date" name="date" value="<?php echo $resultat['date'] ?>"</input>
				</div>
				<div class="mb-3">
					<label for="heure_debut" class="form-label">Début :</label><br>
					<input type="time" step="300" name="heure_debut" value=<?php echo $resultat['heure_debut'] ?>>
				</div>
				<div class="mb-3">
					<label for="heure_fin" class="form-label">Fin :</label><br>
					<input type="time" step="300" name="heure_fin" value=<?php echo $resultat['heure_fin'] ?>>
				</div>				
				<div class="mb-3">
					<?php
						// Récupérer le type affaire
						$stmt = $db->query("SELECT type_affaire FROM affaires WHERE id_affaire = " . $resultat['id_affaire']);
						$dernierPointage = $stmt->fetch();
						$typeAffaire = $dernierPointage['type_affaire'];
					?>
					<fieldset>
						<legend class="form-label">Choisir une catégorie :</legend>
						<input type="radio" name="type" value="Affaires" id="Affaires" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Affaire"){echo "checked";}?>>
						<label for="Affaires" class="form-label" style="display: inline-block; margin-right: 10px;">Affaires</label>
						<input type="radio" name="type" value="Devis" id="Devis" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Devis"){echo "checked";}?>>
						<label for="Devis" class="form-label" style="display: inline-block; margin-right: 10px;">Devis</label>
						<input type="radio" name="type" value="Divers" id="Divers" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Divers"){echo "checked";}?>>
						<label for="Divers" class="form-label" style="display: inline-block;">Divers</label>
					</fieldset>
					<label for="num_affaire" class="form-label">N° Affaire :</label><br>
					<select name="num_affaire" id="num_affaire">
						<?php
						$affaires = $db->prepare("SELECT * FROM affaires WHERE (type_affaire = '" . $typeAffaire . "' && actif = 1) ORDER BY num_affaire DESC");
						$affaires->execute();
						while ($affaire = $affaires->fetch()) {
							$selected = '';
							if ($affaire['id_affaire'] == $resultat['id_affaire']) {
								$selected = ' selected';
							}
							echo '<option value="' . $affaire['id_affaire'] . '"' . $selected . '>' . $affaire['num_affaire'] . '-' . $affaire['intitule'] . '</option>';
						}
						?>
					</select>
				</div>				
				<div class="mb-3">
					<label for="tache" class="form-label">Tâche :</label><br>
					<select name="tache" id="tache">
						<option></option>
						<?php
							$taches = $db->query('SELECT DISTINCT type_tache FROM taches ORDER BY type_tache ASC');
							foreach ($taches as $tache) {
								echo '<optgroup label="' . $tache['type_tache'] . '">';
								$tachesOfType = $db->prepare('SELECT * FROM taches WHERE type_tache = :type ORDER BY intitule ASC');
								$tachesOfType->execute(array(':type' => $tache['type_tache']));
								foreach ($tachesOfType as $tacheOfType) {
									$selected = '';
									if ($tacheOfType['id_tache'] == $resultat['id_tache']) {
										$selected = ' selected';
									}
									echo '<option value="' . $tacheOfType['id_tache'] . '"' . $selected . '>' . $tacheOfType['intitule'] . '</option>';
								}
								echo '</optgroup>';
							}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Description :</label><br>
					<textarea cols="60" rows="3" name="description"><?php echo $resultat['description'] ?></textarea>
				</div>	
				<div class="mb-3">
					<label for="trajet" class="form-label">Trajet :</label><br>
					<input name="trajet" value=<?php echo $resultat['trajet'] ?> >
				</div>
				<div class="mb-3">
					<label for="distance" class="form-label">Distance :</label><br>
					<input name="distance" value=<?php echo $resultat['nb_km'] ?> >
				</div>
				<button type="submit" value="valider" class="btn btn-primary">Valider</button>
			</form>
			<br>
		</div>
	</main>
	
	<footer>
		<div class="container">
			<?php include_once('footer.php'); ?>
		</div>
	</footer>
</body>	
</html>