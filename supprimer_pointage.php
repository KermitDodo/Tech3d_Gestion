<?php 
	session_start();

	if(!isset($_SESSION['LOGGED_USER'])):
		header('Location: index.php');
		exit;
	endif; 

	// Récupérer l'identifiant
	$id = $_GET['id'];

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();

	// Supprimer la ligne correspondante dans la base de données
	$requete = $db->prepare('DELETE FROM suivi_heures WHERE id_suivi_heures = :id');
	$requete->execute(array('id' => $id));

	// Rediriger l'utilisateur vers la page d'accueil
	include('tableau_pointage.php');
	exit();
?>