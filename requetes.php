<?php
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

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();

	// Définition des modèles de requête avec cumul d'heures sur une période donnée
	// Intitulé => queryPart1, queryPart2, queryPart3, titre, basé sur affaire
	$queries = array(
		// Pointage par taches sur une affaire
		"Heures par affaire / tache" => array("SELECT taches.intitule as 'Opération',", "FROM suivi_heures JOIN taches ON taches.id_tache = suivi_heures.id_tache WHERE id_affaire = :id_affaire", "GROUP BY suivi_heures.id_tache, id_affaire", 'Pointage par tache - Affaire : ', true),
		// Pointage par employé pour une affaire
		"Heures par affaire / employé" => array("SELECT CONCAT(employes.nom, ' ', employes.prenom) as 'Opérateur',", "FROM suivi_heures JOIN employes ON employes.id_employe = suivi_heures.id_employe WHERE id_affaire = :id_affaire", "GROUP BY suivi_heures.id_employe, id_affaire",'Pointage par employé - Affaire : ', true),
		// Pointage par taches sur les affaires actives de la période --> A VALIDER !
		"Cumul taches par affaires" => array("SELECT affaires.intitule as 'Affaire', taches.intitule as 'Opération',", "FROM suivi_heures JOIN taches ON taches.id_tache = suivi_heures.id_tache JOIN affaires ON affaires.id_affaire = suivi_heures.id_affaire", "GROUP BY suivi_heures.id_affaire, suivi_heures.id_tache", 'Opérations sur la période ', false),
		// Tableau détaillé du pointage (date, debut, fin, tache) sur une affaire
		"Détail heures par affaire" => array("SELECT DATE_FORMAT(date, '%d/%m/%Y') as 'Date', TIME_FORMAT(heure_debut, '%H:%i') as 'Heure Début', TIME_FORMAT(heure_fin, '%H:%i') as 'Heure Fin', taches.intitule as 'Opération',", "FROM suivi_heures JOIN taches ON taches.id_tache = suivi_heures.id_tache WHERE id_affaire = :id_affaire", "ORDER BY date, heure_debut",'Détail pointage - Affaire ', true),
		// Cumul d'heures avec détail par tache pour l'employé connecté
		"Total heures employé / tache" => array("SELECT taches.intitule as 'Opération',", "FROM suivi_heures JOIN taches ON taches.id_tache = suivi_heures.id_tache WHERE id_employe = :id_employe", "GROUP BY suivi_heures.id_tache, id_employe",'Pointage par tache - Employé', false),
		// Pointage de tous les employés actifs sur la période
		"Cumul heures employés" => array("SELECT CONCAT(employes.nom, ' ', employes.prenom) as 'Opérateur',", "FROM suivi_heures JOIN employes ON employes.id_employe = suivi_heures.id_employe", "GROUP BY suivi_heures.id_employe",'Cumul heures employé', false),
	);

	if (isset($_POST['requetes'])) {
		
		// Traiter le formulaire
		$idAffaire = $_POST['num_affaire'];
		$idUser = $_SESSION['LOGGED_USER']['idEmploye'];
		$dateDebut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : '0000-00-00';
		$dateFin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : date("Y-m-d");
		$query = $_POST['requetes'];
		$totalSecondes = 0;

		// Récupérer intitulé affaire
		if ($queries[$query][4] === true) {
			$affaires = $db->prepare('SELECT * FROM affaires WHERE id_affaire =' . $idAffaire);
			$affaires->execute();
			while ($affaire = $affaires->fetch()) {
				$intituleAffaire =  $affaire['num_affaire'] . '-' . $affaire['intitule'];
			}	
		}
		
		// Composer requete
		if ($query != "Détail heures par affaire") {
			$sql = $queries[$query][0] . ' ' . "TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(heure_fin, heure_debut)))), '%H:%i') as 'Total ligne'" . ' ' . $queries[$query][1];
		} else {
			$sql = $queries[$query][0] . ' ' . "TIME_FORMAT(TIMEDIFF(heure_fin, heure_debut), '%H:%i') as 'Total ligne'" . ' ' . $queries[$query][1];		
		}
		
		if ($dateDebut != '0000-00-00') {
			$sql .= " AND date >= :date_debut";
		}
		if ($dateFin != date("Y-m-d")) {
			$sql .= " AND date <= :date_fin";
		}
		$sql .= ' ' . $queries[$query][2];

		// Préparer la requête
		$stmt = $db->prepare($sql);
		if ($queries[$query][4] === true) {
		$stmt->bindParam(':id_affaire', $idAffaire);
		}
		if ($query == "Total heures employé / tache") {
			$stmt->bindParam(':id_employe', $idUser);
		}
		if ($dateDebut != '0000-00-00') {
			$stmt->bindParam(':date_debut', $dateDebut);
		}
		if ($dateFin != date("Y-m-d")) {
			$stmt->bindParam(':date_fin', $dateFin);
		}

		// Exécuter la requête
		$stmt->execute();
		
		// Formater titre de la requête
		$titreTableau = $queries[$query][3];

		if ($queries == "Cumul heures employés") {
			$titreTableau .= $intituleAffaire;
		}
		if ($dateDebut != '0000-00-00') {
			$dateFr = date('d/m/Y', strtotime($dateDebut));
			$titreTableau .= ' - Du ' . $dateFr;
		}
		if ($dateFin != date('0000-00-00')) {
			$dateFr = date('d/m/Y', strtotime($dateFin));
			$titreTableau .= ' Au ' . $dateFr;
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Base de données - Requêtes</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
				<h1 class="titre">Interrogation des données</h1>
			</div>
			<form method="post" display="inline-block" id="form_requete">
				<div>
					<label for="requetes">Modèle :</label><br>
					<select name="requetes" id="requetes">
					<?php 
						foreach($queries as $key => $value) { 
							echo '<option value="' . $key . '">' . $key .'</option>';
						}
					?>
					</select>
				</div>
				<div class="sideBySide">
					<label for="date_debut" class="form-label">Début</label><br>
					<input name="date_debut" id="date_debut" type="date">
				</div>
				<div class="sideBySide">
					<label for="date_fin" class="form-label">Fin</label><br>
					<input name="date_fin" id="date_fin" type="date">
				</div>
				<div>
					<fieldset id="select_type">
						<legend class="form-label">Choisir une catégorie :</legend>
						<input type="radio" name="type" value="Affaires" id="Affaires" style="display: inline-block; width: 30px;" checked>
						<label for="Affaires" class="form-label" style="display: inline-block; margin-right: 10px;">Affaires</label>
						<input type="radio" name="type" value="Devis" id="Devis" style="display: inline-block; width: 30px;">
						<label for="Devis" class="form-label" style="display: inline-block; margin-right: 10px;">Devis</label>
						<input type="radio" name="type" value="Divers" id="Divers" style="display: inline-block; width: 30px;">
						<label for="Divers" class="form-label" style="display: inline-block;">Divers</label><br>
						<label for="num_affaire" class="form-label">N° Affaire :</label><br>
						<select name="num_affaire" id="num_affaire" required>
						<?php
							$affaires = $db->query('SELECT * FROM affaires WHERE type_affaire = "Affaire" ORDER BY num_affaire DESC');
							echo '<option></option>';
							foreach ($affaires as $affaire) {
								echo '<option value="' . $affaire['id_affaire'] . '">' . $affaire['num_affaire'] . '-' . $affaire['intitule'] . '</option>';
							}
						?>
						</select>
					</fieldset>
				</div>
				<div>
					<button id="btn-html" type="submit" name="action" value="btn-html" class="btn btn-primary btn-sm">Générer Tableau</button>
					<button id="btn-csv" type="button" name="action" value="btn-csv" class="btn btn-primary btn-sm">Générer CSV</button>
				</div>
			</form>
			<?php
				if (isset($_POST['requetes'])) {
					// Génération du tableau HTML
					if ($_POST['action'] === 'btn-html') {
						
						// Initialiser une variable pour le total heures
						$total = 0;
						
						// Afficher le titre du tableau
						echo "<p id='titre-table'>" . $titreTableau . "</p>";
						
						// Récupérer les noms de colonnes
						$columnCount = $stmt->columnCount();
						$columnNames = array();
						for ($i = 0; $i < $columnCount; $i++) {
							$columnNames[] = $stmt->getColumnMeta($i)['name'];
						}

						// Afficher les résultats dans un tableau
						echo '<table id="table-requete" class="table table-striped table-borderless table-hover table-sm table-responsive-md">';
						echo '<thead class="thead-dark"><tr>';
						foreach ($columnNames as $columnName) {
							echo '<th>' . $columnName . '</th>';
						}
						echo '</tr></thead>';
						echo '<tbody>';
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<tr>';
							foreach ($row as $value) {
								echo '<td>' . $value . '</td>';
							}
							echo '</tr>';
							if ($query != "Cumul heures employés") {
								// Mettre à jour le total heures
								$timeToSec = strtotime($row['Total ligne']) - strtotime('TODAY');
								$total += $timeToSec;
							}
						}
						// Ajouter une ligne avec le total
						if ($query != "Cumul heures employés") {
							// Convertir le temps total en hh:mm
							$total = sprintf('%02d:%02d', ($total/3600),($total/60%60));
							echo '<tr>';
							for ($i = 0; $i < $columnCount-2; $i++) {echo '<td></td>';}
							echo '<td align="right">Total : </td>';
							echo '<td>' . $total . '</td></tr>';
							echo '</tbody>';
							echo '</table>';
						}
					}
				}
			?>
		</div>
	</main>
	
	<footer>
		<div class="container">
			<?php include_once('footer.php'); ?>
		</div>
	</footer>
	
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="script.js"></script>

	<script>	
		// Détecter les changements dans la balise selectionner requete
		document.getElementById("requetes").addEventListener("change", () => {
			const $selectType = document.getElementById("select_type");
			const $numAffaire = document.getElementById("num_affaire");
			const $requeteValue = document.getElementById("requetes").value;
			
			if ($requeteValue === "Total heures employé / tache" || $requeteValue === "Cumul heures employés" || $requeteValue === "Cumul taches par affaires") {
				$selectType.style.display = "none";
				$numAffaire.removeAttribute("required");
			} else {
				$selectType.style.display = "block";
				$numAffaire.setAttribute("required", true);
			}
		});

		document.getElementById("btn-html").addEventListener("click", () => {
			const btnCsv = document.getElementById("btn-csv");
			const form = document.getElementById("form_requete");			
			form.addEventListener("submit", () => {
			});
		});

		
		document.getElementById("btn-csv").addEventListener("click", () => {
			const btnCsv = document.getElementById("btn-csv");
			const tableHtml = document.getElementById("table-requete").innerHTML;
			const titreHtml = document.getElementById("titre-table").innerHTML;
			const csv = titreHtml + ';\n' + convertToCSV(tableHtml);
			
			const downloadLink = document.createElement("a");
			downloadLink.setAttribute("href", "data:text/csv;charset=utf-8,%EF%BB%BF" + encodeURIComponent(csv));
			downloadLink.setAttribute("download", titreHtml + ".csv");
			downloadLink.style.display = "none";
			document.body.appendChild(downloadLink);
			downloadLink.click();
			document.body.removeChild(downloadLink);
		});
		
		function convertToCSV(html) {
			const table = document.createElement("table");
			table.innerHTML = html;
			const rows = Array.from(table.querySelectorAll("tr"));
			const csvRows = rows.map((row) =>
				Array.from(row.querySelectorAll("td, th")).map((cell, index) => {
					if (index === row.cells.length - 1) {
						return cell.innerText;
					} else {
						return cell.innerText + ";";
					}
				})
			);
			csvRows[0][1] = csvRows[0][1].replace(",", ";");
			return csvRows.map((row) => row.join("")).join("\n");
		}
	</script>
	
</body>
</html>