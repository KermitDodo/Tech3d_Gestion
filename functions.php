<?php
function displayResult($table) {
	if ($_SESSION['LOGGED_USER']['idEmploye'] == 1) {
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
	}
}

function bddConnect() {
	// Charger la configuration depuis le fichier ini
    $config = parse_ini_file('config/config.ini');
    
    // Se connecter à la base de données
    try {
        $db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer la gestion des exceptions
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Désactiver l'émulation des requêtes préparées
        return $db;
    } catch (PDOException $e) {
        // Gérer les erreurs de connexion
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
        return null;
    }
}

?>