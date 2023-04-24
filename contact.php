<?php
	session_start(); 

	// Inclusion de la bibliothèque PHPMailer
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	/* Classe de traitement des exceptions et des erreurs */
	require 'PHPMailer/src/Exception.php';
	/* Classe-PHPMailer */
	require 'PHPMailer/src/PHPMailer.php';
	/* Classe SMTP nécessaire pour établir la connexion avec un serveur SMTP */
	require 'PHPMailer/src/SMTP.php';

	try {
		// Tentative de création d’une nouvelle instance de la classe PHPMailer
		$mail = new PHPMailer(true);
	} catch (Exception $e) {
		echo "Mailer Error: ".$mail->ErrorInfo;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		// Récupération des données du formulaire
		$nom = htmlspecialchars($_POST['nom']);
		$email = htmlspecialchars($_POST['email']);
		$sujet = htmlspecialchars($_POST['sujet']);
		$message = htmlspecialchars($_POST['message']);

		// Validation des données du formulaire
		if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
			// Les champs sont vides, renvoyer l'utilisateur vers la page précédente avec un message d'erreur
			header('Location: contact.php?erreur=champs-vides');
			exit;
		}

		// Configuration de l'envoi d'email
		try {
			//Server settings
			$mail->isSMTP();
			$mail->Host       = 'smtp.ionos.fr';
			$mail->SMTPAuth   = true;
			$mail->Username   = 'v.zanoguera@tech3d-france.com';
			$mail->Password   = 'MorgAli_0305';
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port       = 465;
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);

			//Destinataire
			$mail->setFrom('v.zanoguera@tech3d-france.com');
			$mail->addAddress($email, $nom);

			//Contenu du message
			$mail->isHTML(false);
			$mail->Subject = $sujet;
			$mail->Body    = "Nom: $nom\n\nE-mail: $email\n\nSujet: $sujet\n\nMessage:\n$message";

			$mail->send();
			// L'e-mail a été envoyé avec succès, rediriger l'utilisateur vers une page de confirmation d'envoi
			echo 'Sent OK';
			header('Location: submit_contact.php');
			exit;
		} catch (Exception $e) {
			// L'envoi a échoué, renvoyer l'utilisateur vers la page précédente avec un message d'erreur
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			//header('Location: contact.php?erreur=envoi-echoue');
			//exit;
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech3D Pointage - Contact</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">
	<header>
		<div class="container">
			<?php include_once('header.php'); ?>
		</div>	
	</header>
	
	<main title="principale">
		<div class="container">
			<div>
				<h1 class="titre">Formulaire de contact</h1>
			</div>
			<form action="contact.php" method="post">
				<div class="mb-3">
					<label for="nom">Nom:</label><br>
					<input type="text" id="nom" name="nom" value="<?php echo $_SESSION['LOGGED_USER']['nom'] . " " . $_SESSION['LOGGED_USER']['prenom'] ?>" required>
				</div>
				<div class="mb-3">
					<label for="email">E-mail:</label><br>
					<input type="email" id="email" name="email" value="<?php echo $_SESSION['LOGGED_USER']['email'] ?>" required>
				</div>
				<div class="mb-3">
					<label for="sujet">Sujet:</label><br>
					<input type="text" id="sujet" name="sujet" required>
				</div>
				<div class="mb-3">
					<label for="message">Message:</label><br>
					<textarea id="message" name="message" required></textarea>
				</div>
				<button type="submit" class="btn btn-primary">Envoyer</button>
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