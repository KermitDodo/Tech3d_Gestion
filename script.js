$(document).ready(function() {
	// Détecter les changements dans les balises radio
	$('input[type=radio][name=type]').change(function() {
		// Récupérer la valeur de la balise radio sélectionnée
		var selectedType = $(this).val();

		// Effectuer une requête AJAX pour récupérer les options appropriées
		$.ajax({
			url: 'get_option.php',
			type: 'post',
			data: { type: selectedType },
			success: function(response) {
				// Mettre à jour les options de la balise select avec les options récupérées
				$('#num_affaire').html(response);
			}
		});
	});
	
	/*// Détecter les changements dans la balise select
	$('#requetes').on('change', function() {
		if ($(this).val() === 'Total heure employé') {
			$('#select_type').hide();
			$('#num_affaire').removeAttr('required');
		} else {
			$('#select_type').show();
			$('#num_affaire').Attr('required', true);
		}
	});*/
});
