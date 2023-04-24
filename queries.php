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
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include_once('functions.php');

// Se connecter à la BDD
$db = bddConnect();

$affaires = [
	"lireTous" => function ($type) use ($db) {
		// Requête pour sélectionner toutes les affaires
		$affaireStmt = $db->prepare("SELECT * FROM affaires WHERE (type_affaire = ?) ORDER BY num_affaire DESC");
		$affaireStmt->execute([$type]);
		return $affaireStmt->fetchAll();
	},	
	"lireActifs" => function ($type) use ($db) {
		// Requête pour sélectionner toutes les affaires actives
		$affaireStmt = $db->prepare("SELECT * FROM affaires WHERE (type_affaire = ? AND actif = 1) ORDER BY num_affaire DESC");
		$affaireStmt->execute([$type]);
		return $affaireStmt->fetchAll();
	}
];

$employes = [
	"lireTous" => function ($id, $actif) use ($db) {
		// Requête pour sélectionner tous les employes actifs
		$employeStmt = $db->prepare("SELECT * FROM employes WHERE (id_employe = ? AND actif = ?) ORDER BY nom DESC");
		$employeStmt->execute([$id], $actif);
		return $employeStmt->fetchAll();
	},	
	"lireUn" => function ($id) use ($db) {
		// Requête pour sélectionner un employe
		$employeStmt = $db->prepare("SELECT * FROM employes WHERE (id_employe = ?)");
		$employeStmt->execute([$id]);
		return $employeStmt->fetch();
	}
];
