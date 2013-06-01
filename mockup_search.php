<?php

$q='';
if (isset($_GET['q']))
{
	$q = trim($_GET['q']);
}

?>
<!DOCTYPE html>
<html>
<head>
<!--	<base href="/bionames-api/" /> -->
	<base href="/" />
	<title>Search</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
</head>
<body class="search">
	<?php require 'analyticstracking.inc.php'; ?>	
	<?php require 'navbar.inc.php'; ?>
	

	<div class="container-fluid">
		<div class="row-fluid">
		    <div class="main-content span8">
			  <div id="results"></div>
		    </div>
	  		
	  		<div class="sidebar span4">
				<div class="sidebar-header">
					<h1 id="title"><?php echo($q); ?></h1>
				</div>
				<div id="metadata" class="sidebar-metadata">
					<div id="stats" class="stats"></div>
				</div>
				<div id="didyoumean" class="sidebar-section"></div>
	  		</div>
		</div>
	</div>

	<script>
	
		var $metadata_stats = $('#stats');
	
		function add_metadata_stat(title,value,anchor) {
			$(display_stat(title,value,anchor)).appendTo($metadata_stats);
		}
	
		function search(q) {
		
//			$.getJSON("http://bionames.org/bionames-api/search/" + encodeURIComponent(q) + "?callback=?",
			$.getJSON("api/search/" + encodeURIComponent(q) + "?callback=?",
			function(data) {
			  if (data.status == 200) {
			    var html = '';

			    if (data.results) {
			      var ids = [];

			      // order in which we want to display facets
				  
				  var facet_display_categories = [
				  	{ name: 'Names',         facet_keys: ['nameCluster'] },
					{ name: 'Taxa' ,         facet_keys: ['taxonConcept'] },
					{ name: 'Articles' ,     facet_keys: ['article'] },
					{ name: 'Publications' , facet_keys: ['book', 'chapter', 'generic'] },
				  ];
				  
				  
				  for(var f in facet_display_categories){
					  var facet = facet_display_categories[f];
					  var facet_class = facet.name.toLowerCase().replace(/\W/, '-');
					  var facet_id = 'facet-' + facet_class;
					  var facet_html = '';
					  var results_in_facet = 0;
					  
					  facet_html += '<div class="facet '+ facet_class +'" id="'+facet_id+'">';
					  facet_html +=   '<div class="facet-title"><h2>' + facet.name + '</h2></div>';
					  facet_html +=   '<div class="cards">';
					  
					  
					  for(var fk in facet.facet_keys) {
						var facet_key = facet.facet_keys[fk];
						if(data.results.facets[facet_key]){
							for(var id in data.results.facets[facet_key]) {
								
								var skip = false;
								if ((facet_key == 'nameCluster') && (results_in_facet == 5)) {
									skip = true;
								}
								if (!skip) {								
									results_in_facet++;
									var html_id = id.replace(/\//, '_');
									var result = data.results.facets[facet_key][id];
									
									if(facet.name == 'Names') {
	//								  facet_html += '<div class="name-cluster snippet-wrapper"><a href="mockup_taxon_name.php?id=' + id + '">' + result.term + '</a></div>';
									  facet_html += '<div class="name-cluster snippet-wrapper"><a href="names/' + id + '">' + result.term + '</a></div>';
									} else {
									  ids.push(id);	
									  facet_html += '<div id="id'+html_id+'" class="snippet-wrapper"><span class="loading">loading</span></div>';
									}
								}
							}
						}
					  }
					  
					  facet_html +=   '</div>'; // div.cards
					  facet_html += '</div>';   // div.facet
					
		              add_metadata_stat(facet.name, results_in_facet);
					
					  if(results_in_facet > 0) {
						  html += facet_html;
					  }
				  }
			    for (var id in ids) {
			      html += '<script>display_snippets("' + ids[id] + '");<\/script>';
			    }


			    $('#results').html(html);
			  }
		    }
		  });
		}
	
	
		function did_you_mean(name)
		{
			$("#didyoumean").html("");
			
//			$.getJSON("http://bionames.org/bionames-api/name/" + encodeURIComponent(name) + "/didyoumean?callback=?",
			$.getJSON("api/name/" + encodeURIComponent(name) + "/didyoumean?callback=?",
				function(data){
					if (data.status == 200)
					{		
						var html = '';
						if (data.names.length > 0) {
							html += '<h3>Did you mean</h3>';
							html += '<ul>';
							
							for (var i in data.names) {
								html += '<li>';
//								html += '<a href="mockup_search.php?q=' + encodeURIComponent(data.names[i]) + '">' + data.names[i] + '</a>';
								html += '<a href="search/' + encodeURIComponent(data.names[i]) + '">' + data.names[i] + '</a>';
								html += '</li>';
							}
							html += '</ul>';
							
							$("#didyoumean").html(html);
						}
					}
				});
		}
	</script>


<?php
	echo '<script>
		search(\'' . addcslashes($q, "'") . '\');
		did_you_mean(\'' . addcslashes($q, "'") . '\');
	</script>';
?>	
	
	
	<!-- typeahead for search box -->
	<script>
	$("#q").typeahead({
	  source: function (query, process) {
//		$.getJSON('http://bionames.org/bionames-api/name/' + query + '/suggestions?callback=?', 
		$.getJSON('api/name/' + query + '/suggestions?callback=?', 
		function (data) {
		  //data = ['Plecopt', 'Peas'];
		  
		  var suggestions = data.suggestions;
		  process(suggestions)
		})
	  }
	})
	</script>		
	
	


	<script> if (use_livereload) { document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>'); }</script>

</body>
</html>