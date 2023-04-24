<!DOCTYPE html>
<html>
<head>
    <title>Formulaire JavaScript</title>
</head>
<body>

<h1>Formulaire d'enregistrement des utilisateurs</h1>
<form id="formUser">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom"><br>
    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom"><br>
    <label for="pseudo">Pseudo :</label>
    <input type="text" id="pseudo" name="pseudo"><br>
    <input type="submit" value="Enregistrer" id="btnEnregistrerUser">
</form>

<h1>Formulaire d'enregistrement des clients</h1>
<form id="formClient">
    <label for="marque">Marque :</label>
    <input type="text" id="marque" name="marque"><br>
    <label for="contact">Contact :</label>
    <input type="text" id="contact" name="contact"><br>
    <label for="telephone">Téléphone :</label>
    <input type="text" id="telephone" name="telephone"><br>
    <input type="submit" value="Enregistrer" id="btnEnregistrerClient">
</form>

<script>
document.getElementById("formUser").addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire

    // Récupère les valeurs des champs de formulaire
    var nom = document.getElementById("nom").value;
    var prenom = document.getElementById("prenom").value;
    var pseudo = document.getElementById("pseudo").value;

    // Vérification que les champs sont remplis
    if (nom !== "" && prenom !== "" && pseudo !== "") {
        // Création de l'objet de données pour l'utilisateur
        var userData = {
            nom: nom,
            prenom: prenom,
            pseudo: pseudo
        };

        // Appel à l'API pour enregistrer les données de l'utilisateur
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Erreur lors de l\'enregistrement de l\'utilisateur : ' + response.status);
            }
        })
        .then(function(data) {
            // Traitement de la réponse JSON renvoyée par le serveur
            console.log('Enregistrement de l\'utilisateur réussi :', data);
        })
        .catch(function(error) {
            // Gestion des erreurs
            console.error(error);
        });
    } else {
        alert("Veuillez remplir tous les champs du formulaire d'utilisateur.");
    }
});


document.getElementById("formClient").addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire

    // Récupère les valeurs des champs de formulaire
    var marque = document.getElementById("marque").value;
    var contact = document.getElementById("contact").value;
    var telephone = document.getElementById("telephone").value;

    // Vérification que les champs sont remplis
    if (marque !== "" && contact !== "" && telephone !== "") {
        // Création de l'objet de données pour le client
        var clientData = {
            marque: marque,
            contact: contact,
            telephone: telephone
        };

        // Appel à l'API pour enregistrer les données du client
        fetch('/api/clients', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(clientData)
        })
        .then(function(response) {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Erreur lors de l\'enregistrement du client : ' + response.status);
            }
        })
        .then(function(data) {
            // Traitement de la réponse JSON renvoyée par le serveur
            console.log('Enregistrement du client réussi :', data);
        })
        .catch(function(error) {
            // Gestion des erreurs
            console.error(error);
        });
    } else {
        alert("Veuillez remplir tous les champs du formulaire de client.");
    }
});

</script>

</body>
</html>