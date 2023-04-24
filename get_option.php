<?php

    /*-----------------------------------------------------------------*/
    /* Générer les options en fonction de la valeur de la balise radio */
    /*-----------------------------------------------------------------*/

	include_once('functions.php');

	// Se connecter à la BDD
	$db = bddConnect();

    // Récupérer la valeur de la balise radio envoyée par la requête AJAX
    $type = $_POST['type'];
    $type = ($type === 'Affaires') ? 'Affaire' : $type;

    // Générer une ligne vide
    echo '<option></option>';

    // Extraire les données de la base affaires en fonction de la demande
    $affaires = $db->query("SELECT * FROM affaires WHERE type_affaire = '$type' AND actif = 1 ORDER BY num_affaire DESC");

    // Copier la liste dans la balise
    foreach ($affaires as $affaire) {
        echo '<option value="' . $affaire['id_affaire'] . '">' . $affaire['num_affaire'] . '-' . $affaire['intitule'] . '</option>';
    }

?>

