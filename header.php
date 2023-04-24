<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech3D Pointage</title>
	<link rel="icon" type="image/png" href="img/favicon_tech3d.png">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> 
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-expand-md navbar-light bg-light">
        <div class="container-fluid">
            <!-- Logo de la navbar -->
			<a class="navbar-brand" href="index.php">
				<img class="logoEnTete" src="img/logo_tech3d_flat.jpg" alt="Logo Tech3D">
			</a>
            <!-- Bouton pour afficher ou cacher le menu sur mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu du menu -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Saisie</a>
                    </li>

                    <!-- Afficher les liens de consultation et d'interrogation uniquement si l'utilisateur est connecté -->
                    <?php if(isset($_SESSION['LOGGED_USER'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="tableau_pointage.php">Consultation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="requetes.php">Interrogation</a>
                        </li>

                        <!-- Afficher le lien de gestion BDD uniquement si l'utilisateur a un grade de 1 -->
                        <?php if($_SESSION['LOGGED_USER']['grade'] === 1) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="gestion.php">Gestion BDD</a>
                            </li>
                        <?php } ?>

                    <?php endif; ?>

                    <!-- Lien de contact -->
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>

            <?php if(isset($_SESSION['LOGGED_USER'])) {
			
				// Récupération des initiales de l'utilisateur
				$initiales = substr($_SESSION['LOGGED_USER']['prenom'], 0, 1) . substr($_SESSION['LOGGED_USER']['nom'], 0, 1);
			?>

			<div class="btn-group dropstart">
				<!-- Bouton avec les initiales qui ouvre le menu -->
				<button type="button" class="userIcon dropdown-toggle" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $initiales ?></button>
				<ul class="dropdown-menu dropdown-menu-end">
					<!-- Lien vers les paramètres -->
					<li><a class="dropdown-item" href="parametres.php">Paramètres</a></li>
					<!-- Lien de déconnexion -->
					<li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
				</ul>
			</div>
			
            <?php } ?>
        </div>
    </nav>
</body>

</html>