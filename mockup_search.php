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
	<base href="//bionames.org/" /><!--[if IE]></base><![endif]-->
	<title><?php if ($q != '') { echo htmlspecialchars($q) . ' - ' ; } ?>BioNames Search</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
	<script src="treelib-js/treelib.js"></script>	
	
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
		
		function display_trees(id) {
			$.getJSON("api/tree/" + id + "?callback=?",
				function(data){
					if (data.status == 200)
					{		
						var t = new Tree();
						t.Parse(data.tree.newick);

						if (t.error != 0)
						{
						}
						else
						{							
							t.ComputeWeights(t.root);		
							var td = new CirclePhylogramDrawer();
							td.Init(t, {svg_id: id.replace(/\//, '_'), width:88, height:88, fontHeight:0, root_length:0.1} );		
							td.CalcCoordinates();
							td.Draw();
						}
						
						if (data.tags) {
							$('#tag' + id.replace(/\//, '_')).html(data.tags[0]);
						}
						
					}
				});
		}		
	
		function search(q) {
		
//			$.getJSON("//bionames.org/bionames-api/search/" + encodeURIComponent(q) + "?callback=?",
			$.getJSON("api/search/" + encodeURIComponent(q) + "?callback=?",
			function(data) {
			  if (data.status == 200) {
			    var html = '';

			    if (data.results) {
			      var ids = [];
			      
			      var trees = [];

			      // order in which we want to display facets
				  
				  var facet_display_categories = [
				  	{ name: 'Names',         facet_keys: ['nameCluster'] },
					{ name: 'Taxa',          facet_keys: ['taxonConcept'] },
					{ name: 'Phylogenies',   facet_keys: ['tree'] },
					{ name: 'Articles',      facet_keys: ['article'] },
					{ name: 'Publications',  facet_keys: ['book', 'chapter', 'generic'] },
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
									//facet_html += '<div class="snippet-wrapper">more...</div>';
									/*facet_html += '<div class="cards accordion" id="accordion" >';
									facet_html += '<div class="class="accordion-heading" >';
									facet_html +=  '  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse'">';
									facet_html +=  'more';
									facet_html += '</a>';
									facet_html += '</div>';*/
									
								}
								if (!skip) 
								{								
									results_in_facet++;
									var html_id = id.replace(/\//g, '_');
									html_id = html_id.replace(/-/g, '_');
									html_id = html_id.replace(/\./g, '_');
									html_id = html_id.replace(/\(/g, '_');
									html_id = html_id.replace(/\)/g, '_');
									html_id = html_id.replace(/\[/g, '_');
									html_id = html_id.replace(/\]/g, '_');
									html_id = html_id.replace(/;/g, '_');
									html_id = html_id.replace(/:/g, '_');
									html_id = html_id.replace(/</g, '_');
									html_id = html_id.replace(/>/g, '_');
									
									var result = data.results.facets[facet_key][id];
									
									switch (facet.name)
									{
										/*
										case 'Names':
									  		facet_html += '<div class="name-cluster snippet-wrapper"><a href="names/' + id + '">' + result.term + '</a></div>';
									  		break;
									  	*/
									  		
									  	case 'Phylogenies':
									  		trees.push(id);
									  	
									  		facet_html += '<div style="border:1px solid rgb(228,228,228);float:left;margin:10px;background-color:white;">';
											facet_html += '<a href="trees/' + id + '" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'search\', \'phylogeny\', 0]);">';
											facet_html += '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="88" width="88">';
											facet_html += '<g id="' + id.replace(/\//, '_') + '"></g>'; 								
											facet_html += '</svg>';		
											facet_html += '</a>';
											facet_html += '<div id="tag' + id.replace(/\//, '_') + '" style="width:88px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></div>';
											facet_html += '</div>';
									  		break;
									  		
									  	default:
										  	ids.push(id);	
									  		facet_html += '<div id="id'+html_id+'" class="snippet-wrapper"><span class="loading">loading</span></div>';
									  		break;
									  	
									}
								}
							}
							/*
							if ((facet_key == 'nameCluster') && (results_in_facet > 5)) {
									//skip = true;
									facet_html += '</div>';
							}
							*/
						}
					  }
					  
					  facet_html +=   '</div>'; // div.cards
					  facet_html += '</div>';   // div.facet
					
		              add_metadata_stat(facet.name, results_in_facet);
					
					  if(results_in_facet > 0) {
						  html += facet_html;
					  }
				  }
			    
			    for (var id in trees) {
			    	html += '<script>display_trees("' + trees[id] + '");<\/script>';
			    }
			    
			    for (var id in ids) {	
			      html += '<script>display_snippets("' + ids[id] + '", "search");<\/script>';
			    }
			    

			    $('#results').html(html);
			  }
		    }
		  });
		}
	
	
		function did_you_mean(name)
		{
			$("#didyoumean").html("");
			
//			$.getJSON("//bionames.org/bionames-api/name/" + encodeURIComponent(name) + "/didyoumean?callback=?",
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
//		$.getJSON('//bionames.org/bionames-api/name/' + query + '/suggestions?callback=?', 
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