<?php 
	// Ouverture de la session
	if (!isset($_SESSION)) {
		session_start();
	}

	//Fermer la session
	unset($_SESSION['LOGGED_USER']);

	// Rediriger l'utilisateur vers la page d'accueil
	header('Location: index.php');
	exit();
?>
