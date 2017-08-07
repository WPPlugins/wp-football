// JavaScript Document
jQuery(document).ready(function() {
	jQuery('#league_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_teams_list.php";
		var id_league = jQuery(this).val();
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_league },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	 

		var data = {
			action: 'my_action_groups',
			id: id_league
		};
		jQuery("#groups").html('<option value="Wait...">Wait...</option>');
		if (document.getElementById('teamGroups')) jQuery("#teamGroups").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#groups").html(options);
			if (document.getElementById('teamGroups')) jQuery("#teamGroups").html(options);
			}
		})
	});

	jQuery('#match_league_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_matches_list.php";
		var id_league = jQuery(this).val();
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_league },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	 

		var data = {
			action: 'my_action_phases',
			id: id_league
		};
		jQuery("#phases").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#phases").html(options);
			}
		})

		var data = {
			action: 'my_action_groups',
			id: id_league
		};
		jQuery("#groups").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#groups").html(options);
			}
		})
	});

	jQuery('#group_league_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_groups_list.php";
		var id_league = jQuery(this).val();
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_league, byajax: '1' },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	
	});	

	jQuery('#phase_league_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_phases_list.php";
		var id_league = jQuery(this).val();
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_league },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	
	});	

	jQuery('#category_template_select').change(function(e){
		if (jQuery(this).val() == 1) {												
			jQuery('#program').addClass('disabled');
			jQuery('#program').attr('disabled','disabled');
		}
		else {
			jQuery('#program').removeClass('disabled');
			jQuery('#program').removeAttr('disabled');
		}
	});	

	jQuery('#groups').change(function(e){
		var id_group = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_teams_load.php";
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_group },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				results = eval(data);
				var options = '';
				for (var i = 0; i < results.length; i++) {
					options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
				}
				jQuery('#team1').empty();
				jQuery('#team1').html(options);
				jQuery('#team2').empty();
				jQuery('#team2').html(options);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	
	})

	jQuery('#results_league_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var id_league = jQuery(this).val();

		var data = {
			action: 'my_action_phases',
			id: id_league
		};
		jQuery("#results_phase").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#results_phase").html(options);
			}
		})

		var data = {
			action: 'my_action_groups',
			id: id_league
		};
		jQuery("#results_group").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#results_group").html(options);
			}
		})
	});

	jQuery('#results_group').change(function(e){
		jQuery(".classification").hide();
		jQuery("#team1").text('');
		jQuery("#team2").text('');
		jQuery("#score1").val('');
		jQuery("#score2").val('');
		jQuery("#id_match").val('0');
		var id_league = jQuery('#results_league_select').val();
		var id_phase = jQuery('#results_phase').val();
		var id_group = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_matches_load.php";
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id_league: id_league, id_phase: id_phase, id_group: id_group },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	
	})

	jQuery('#update_phase_select').change(function(e){
		document.getElementById('id_league').value = jQuery(this).val();
		var id_league = jQuery(this).val();

		var data = {
			action: 'my_action_phases',
			id: id_league
		};
		jQuery("#phase_u").html('<option value="Wait...">Wait...</option>');

		jQuery.ajax({url: "admin-ajax.php", type: "POST", data: data, success: function(results) {
			results = eval(results);
			var options = '';
			for (var i = 0; i < results.length; i++) {
				options += '<option value="' + results[i].id + '">' + results[i].nome + '</option>';
			}
			jQuery("#phase_u").html(options);
			}
		})
	});

	jQuery('#phase_u').change(function(e){
		id_phase = jQuery(this).val();
		var url = "../wp-content/plugins/wp-football/football_matches_phase.php";
		var id_league = document.getElementById('id_league').value;
		jQuery.ajax({
			url: url,
			dataType: 'html',
			type: 'GET',
			data: { id: id_league, id_phase: id_phase },
			beforeSend: function() {
				jQuery('#extra').show();
			},
			complete: function() {
				jQuery('#extra').hide();
			},
			success: function(data, textStatus) {
				jQuery('#resposta').empty();
				jQuery('#resposta').html(data);
			},
			error: function(xhr,er) {
				alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
			}
		});	 
	});

	jQuery(document).ready(function(){
		jQuery("a[rel^='prettyPhoto']").prettyPhoto({
			animationSpeed: 'normal', /* fast/slow/normal */
			padding: 40, /* padding for each side of the picture */
			opacity: 0.6, /* Value betwee 0 and 1 */
			showTitle: true, /* true/false */
			allowresize: true, /* true/false */
			counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
			theme: 'light_rounded', /* light_rounded / dark_rounded / light_square / dark_square */
			callback: function(){}
		});
	});

});
