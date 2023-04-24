<?php 
	session_start();

	if(!isset($_SESSION['LOGGED_USER'])):
	header('Location: index.php');
	exit;
	endif; 

	// Afficher les erreurs
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Consulter/Modifier le pointage</title>
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
				<h4 class="titre">Supprimer / Modifier les lignes</h4>
			</div>
			<div>
				<table class="table table-striped table-borderless table-hover table-sm table-responsive-md">
					<thead class="thead-dark">
						<tr>
							<th>Edition</th>
							<th>Date</th>
							<th>Debut</th>
							<th>Fin</th>
							<th>Affaire</th>
							<th>Tache</th>
							<th>Description</th>
							<th>Trajet</th>
							<th>Dist.</th>
						</tr>
					</thead>
					<tbody>
						<?php
							// Paramètres de pagination
							$recNombre = 10;
							$page = isset($_GET['page']) ? $_GET['page'] : 1;

							// Calculer le début et la fin des enregistrements à afficher
							$recDebut = ($page - 1) * $recNombre;

							// Récupérer les enregistrements à afficher pour la page courante
							$idEmploye = $_SESSION['LOGGED_USER']['idEmploye'];
							$pointage = $db->query('SELECT * FROM suivi_heures WHERE id_employe = ' . $idEmploye . ' ORDER BY date DESC, heure_debut DESC LIMIT ' . $recDebut . ' , ' . $recNombre);

							// Calculer le nombre total d'enregistrements
							$recTotal = $db->query("SELECT COUNT(*) FROM suivi_heures WHERE id_employe = $idEmploye")->fetchColumn();

							// Calculer le nombre total de pages
							$totalPages = ceil($recTotal / $recNombre);
						
							// Afficher les données dans le tableau HTML
							foreach ($pointage as $row) {
								$idAffaire = $row['id_affaire'];
								$affaires = $db->query("SELECT * FROM affaires WHERE id_affaire = $idAffaire");
								foreach ($affaires as $affaire) {
									$intituleAffaire = $affaire['num_affaire'] . '-' . $affaire['intitule'];
								}
								$idTache = $row['id_tache'];
								if ($idTache != 0) {
									$taches = $db->query("SELECT * FROM taches WHERE id_tache = $idTache");
									foreach ($taches as $tache) {
										$intituleTache = $tache['intitule'];
									}
								} else {
									$intituleTache = ' ';	
								};
								echo '<tr>';
									echo '<td style="text-align: center" width=100px>';
									echo '<a href="modifier_pointage.php?id=' . $row['id_suivi_heures'] . '"><img class="editImg" src="img/edit_ico.png"></a>';
									echo '<a href="supprimer_pointage.php?id=' . $row['id_suivi_heures'] . '"><img class="editImg" src="img/del_ico.png"></a>';
									echo '</td>';
										setlocale(LC_TIME, 'fr_FR');
									echo '<td style="text-align: center" width=100px>' . strftime('%a %d/%m/%Y', strtotime($row['date'])) . '</td>';
									echo '<td style="text-align: center" width=80px>' . $row['heure_debut'] . '</td>';
									echo '<td style="text-align: center" width=80px>' . $row['heure_fin'] . '</td>';
									echo '<td>' . $intituleAffaire . '</td>';
									echo '<td>' . $intituleTache . '</td>';
									echo '<td>' . $row['description'] . '</td>';
									echo '<td>' . $row['trajet'] . '</td>';
									if ($row['nb_km'] != 0) {
										echo '<td style="text-align: center" width=40px>' . $row['nb_km'] . '</td>';
									} else {
										echo '<td width=40px>' . '' . '</td>';
									};
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
			</div>
			
			<!-- Insérer une barre de navigation s'il y a plus d'une page -->
			<nav aria-label="Page navigation">
				<ul class="pagination">
				<?php if ($page > 1): ?>
					<li class="page-item">
						<a class="page-link" href="?page=<?php echo ($page-1); ?>" aria-label="Précédent">
							<span aria-hidden="true">&laquo;</span>
							<span class="sr-only">Précédent</span>
						</a>
					</li>
				<?php endif; ?>
					<li class="page-item active"><a class="page-link" href="#"><?php echo $page . ' / ' . $totalPages; ?></a></li>
				<?php if ($page < $totalPages): ?>
					<li class="page-item">
						<a class="page-link" href="?page=<?php echo ($page+1); ?>" aria-label="Suivant">
							<span class="sr-only">Suivant</span>
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				<?php endif; ?>
				</ul>
			</nav>
			
		</div>
	</main>
	
	<footer>
		<div class="container">
			<?php include_once('footer.php'); ?>
		</div>
	</footer>
</body>
</html>