<?php
	// Ouverture de la session
	if (!isset($_SESSION)) {
		session_start();
	}

	if (!isset($_SESSION['LOGGED_USER'])) {
		header('Location: index.php');
		exit;
	}

	include_once('queries.php');
	include_once('functions.php');

	// Afficher les erreurs
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	// Se connecter à la BDD
	$db = bddConnect();
	
	// Verifier si le formulaire a été retourné
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Récupérer les données du formulaire
		$date = $_POST['date'];
		$heureDebut = $_POST['heure_debut'];
		$heureFin = $_POST['heure_fin'];
		$idEmploye = $_SESSION['LOGGED_USER']['idEmploye'];
		$idAffaire = $_POST['num_affaire'];
		$idTache = $_POST['tache'];
		$description = $_POST['description'];
		$trajet = $_POST['trajet'];
		$distance = $_POST['distance'];
		
		// Détecter nouveau ou reprise pointage
		$idEmploye = $_SESSION['LOGGED_USER']['idEmploye'];
		$stmt = $db->prepare('SELECT * FROM suivi_heures WHERE id_employe = :idEmploye ORDER BY date DESC, heure_debut DESC LIMIT 1');
		$stmt->execute(['idEmploye' => $idEmploye]);
		$dernierPointage = $stmt->fetch();
		$idPointage = $dernierPointage['id_suivi_heures'];
		$reprisePointage = $dernierPointage['heure_fin'] == "00:00:00" ? true : false;
		
		if (!empty($idAffaire) && !empty($date) && !empty($heureDebut)) {    
			// Définir la requête SQL
			$sqlQuery = $reprisePointage
				? 'UPDATE suivi_heures SET date = :date, heure_debut = :heure_debut, heure_fin = :heure_fin, id_employe = :id_employe, id_affaire = :id_affaire, id_tache = :id_tache, description = :description, trajet = :trajet, nb_km = :distance WHERE id_suivi_heures = :id_suivi_heures'
				: 'INSERT INTO suivi_heures (date, heure_debut, heure_fin, id_employe, id_affaire, id_tache, description, trajet, nb_km) VALUES (:date, :heure_debut, :heure_fin, :id_employe, :id_affaire, :id_tache, :description, :trajet, :distance)';

			// Préparer la requête SQL
			$insertPointage = $db->prepare($sqlQuery);

			// Définir les paramètres de la requête SQL
			$params = [
				'date' => $date,
				'heure_debut' => $heureDebut,
				'heure_fin' => $heureFin,
				'id_employe' => $idEmploye,
				'id_affaire' => $idAffaire,
				'id_tache' => $idTache,
				'description' => $description,
				'trajet' => $trajet,
				'distance' => $distance,
			];

			// Ajouter l'identifiant de suivi des heures si la requête est de type UPDATE
			if ($reprisePointage) {
				$params['id_suivi_heures'] = $idPointage;
			}

			// Exécuter la requête SQL
			$insertPointage->execute($params);
		}

	}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech3D Pointage - Saisie</title>
 	<link rel="icon" type="image/png" href="img/favicon_tech3d.png">
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
		<div class="container">
			<h1 class="titre">Saisie des opérations</h1>
			<!-- Inserer formulaire -->
			<form method="post">		
				<?php
					$idEmploye = $_SESSION['LOGGED_USER']['idEmploye'];

					$stmt = $db->query('SELECT * FROM suivi_heures WHERE id_employe = ' . $idEmploye . ' ORDER BY date DESC, heure_debut DESC LIMIT 1');
					$dernierPointage = $stmt->fetch();

					$reprisePointage = $dernierPointage['heure_fin'] == "00:00:00" ? true : false;
					$lastDate = $dernierPointage['date'];
					$lastBegin = !$reprisePointage ? $dernierPointage['heure_fin'] : $dernierPointage['heure_debut'];
					$lastDescription = '';
					$lastDestination = '';
					$lastDistance = '';

					if ($reprisePointage === true) {
						$lastDescription = $dernierPointage['description'];
						$lastDestination = $dernierPointage['trajet'];
						$lastDistance = $dernierPointage['nb_km'];
					}
				
					// Récupérer le type affaire
					$stmt = $db->query("SELECT type_affaire FROM affaires WHERE id_affaire = " . $dernierPointage['id_affaire']);
					$derniereAffaire = $stmt->fetch();
					$typeAffaire = $derniereAffaire['type_affaire'];
				?>	
				
				<div class="mb-3">
					<label for="date" class="form-label">Date<span class="requis">*</span> :</label><br>
					<input type="date" name="date" id="date" value="<?php echo $dernierPointage['date'] ?>" required>
				</div>
				<div class="mb-3">
					<label for="heure_debut" class="form-label">Début<span class="requis">*</span> :</label><br>
					<input type="time" step="300" name="heure_debut" id="heure_debut" value=<?php echo $lastBegin ?> required>
				</div>
				<div class="mb-3">
					<label for="heure_fin" class="form-label">Fin :</label><br>
					<input type="time" step="300" name="heure_fin" id="heure_fin">
				</div>				
				<div class="mb-3">
					<fieldset>
						<legend class="form-label">Choisir une catégorie :</legend>
						<input type="radio" name="type" value="Affaires" id="Affaires" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Affaire"){echo "checked";}?>>
						<label for="Affaires" class="form-label" style="display: inline-block; margin-right: 10px;">Affaires</label>
						<input type="radio" name="type" value="Devis" id="Devis" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Devis"){echo "checked";}?>>
						<label for="Devis" class="form-label" style="display: inline-block; margin-right: 10px;">Devis</label>
						<input type="radio" name="type" value="Divers" id="Divers" style="display: inline-block; width: 30px;" <?php if($typeAffaire == "Divers"){echo "checked";}?>>
						<label for="Divers" class="form-label" style="display: inline-block;">Divers</label>
					</fieldset>
					<label for="num_affaire" class="form-label">N° Affaire<span class="requis">*</span> :</label><br>
					<select name="num_affaire" id="num_affaire" required>
						<?php
						// Récupérer la liste des affaires
						$affaires = $affaires["lireActifs"]($typeAffaire); 
						echo '<option></option>';
						foreach ($affaires as $affaire) {
							$selected = ($affaire['id_affaire'] == $dernierPointage['id_affaire'] && $reprisePointage === true) ? ' selected' : '';
							echo '<option value="' . $affaire['id_affaire'] . '"' . $selected . '>' . $affaire['num_affaire'] . '-' . $affaire['intitule'] . '</option>';
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="tache" class="form-label">Tâche :</label><br>
					<select name="tache" id="tache">
						<?php
						$taches = $db->query('SELECT DISTINCT type_tache FROM taches ORDER BY type_tache ASC');
						echo '<option></option>';
						foreach ($taches as $tache) {
							echo '<optgroup label="' . $tache['type_tache'] . '">';
							$tachesOfType = $db->prepare('SELECT id_tache, intitule FROM taches WHERE type_tache = :type ORDER BY intitule ASC');
							$tachesOfType->execute([':type' => $tache['type_tache']]);
							foreach ($tachesOfType as $tacheOfType) {
								$selected = ($tacheOfType['id_tache'] == $dernierPointage['id_tache'] && $reprisePointage === true) ? ' selected' : '';
								echo '<option value="' . $tacheOfType['id_tache'] . '"' . $selected . '>' . $tacheOfType['intitule'] . '</option>';
							}							
							echo '</optgroup>';
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Description :</label><br>
					<textarea rows="3" name="description" id="description"><?php echo $lastDescription ?></textarea>
				</div>	
				<div class="mb-3">
					<label for="trajet" class="form-label">Trajet :</label><br>
					<input name="trajet" id="trajet" value="<?php echo $lastDestination ?>">
				</div>
				<div class="mb-3">
					<label for="distance" class="form-label">Distance :</label><br>
					<input name="distance" id="distance" value="<?php echo $lastDistance ?>">
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
	
</body>
</html>
