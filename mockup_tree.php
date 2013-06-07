<?php

// mockup template

// do PHP stuff here to get query parameters...
if (isset($_GET['tree']))
{
	$tree = $_GET['tree'];
}


?>
<!DOCTYPE html>
<html>
<head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
	<title>Title</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
	<script src="treelib-js/treelib.js"></script>   	
	<script src="treelib-js/nexus.js"></script>   	
	<script src="vendor/jquery-svgpan/jquery-svgpan.js"></script>
	
	<script>
		var nexus_text;
	</script>
	
</head>
<body class="phylogeny">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid">
		<div class="row-fluid">
		    <div class="main-content span8">
		    
		    
				<ul id="phylogeny-tabs" class="nav nav-tabs">
				  <li class="active"><a href="#view-tab" data-toggle="tab">Phylogeny</a></li>
				  <li><a href="#taxa-tab" data-toggle="tab">Taxa <span id="taxa-badge" class="badge badge-info"></span></a></li>
				  <li><a href="#publications-tab" data-toggle="tab">Publications <span id="publications-badge" class="badge badge-info"></span></a></li>
				  <li><a href="#about-tab" data-toggle="tab">About</span></a></li>
				</ul>
			
				<div class="tab-content">				  
				  <div class="tab-pane active" id="view-tab">
				  
				  
						<div style="text-align:center;">
							<!-- tree viewer -->
							<div style="background-color:white;">
							
								<div>
									<a class="button-radial"><img class="toolbarbutton" src="treelib-js/demos/images/radial.svg"/></a>
									<a class="button-cladogram"><img class="toolbarbutton" src="treelib-js/demos/images/cladogram.svg" /></a>
									<a class="button-rectanglecladogram"><img class="toolbarbutton" src="treelib-js/demos/images/rectangle.svg" /></a>
									<a class="button-phylogram"><img class="toolbarbutton" src="treelib-js/demos/images/phylogram.svg"  /></a>
									<a class="button-circle"><img class="toolbarbutton" src="treelib-js/demos/images/circle.svg"  /></a>
									<a class="button-circlephylogram"><img class="toolbarbutton" src="treelib-js/demos/images/circlephylogram.svg" /></a>
									<!--
									<div style="float:right">
									<a style="display:none;" id="download" href-lang="image/svg+xml" href="" download="tree.svg">
										<img style="padding:2px;border:1px solid rgb(228,228,228);" src="images/download.svg" height="24"/>		
									</a>
									</div>
									-->
								</div>
											
								<!-- tree will be drawn here -->
								<svg id="svg" xmlns="http://www.w3.org/2000/svg" version="1.1" height="600" width="600">
									<g id="viewport"></g>
								</svg>
							</div>
						</div>
						
						<div id="message"></div>				  
				  

				  </div>

				  <div class="tab-pane" id="taxa-tab">
				  	<div id="names">...</div>
				  </div>
				  
				  <div class="tab-pane" id="publications-tab">
				  	<div id="sources"></div>
				  </div>
				  
				  <!-- where we got the data from -->
				  <div class="tab-pane" id="about-tab">
				  	<p>Phylogeny from <i class="icon-share"></i><a href="http://phylota.net/" target="_new">PhyLoTA database</a> version 184, with additional data from EMBL.</p>
				  	<p>Sanderson, M., Boss, D., Chen, D., Cranston, K., & Wehe, A. (2008). The PhyLoTA Browser: Processing GenBank for Molecular Phylogenetics Research. Systematic Biology, 57(3), 335-346. <i class="icon-share"></i><a href="http://dx.doi.org/10.1080/10635150802158688" target="_new">doi:10.1080/10635150802158688</a></p>
				  </div>
				  
				  
				</div>

		    </div>
	  		
	  		<div class="sidebar span4">
				<div class="sidebar-header">
					<h1 id="title"></h1>
				</div>
				<div id="metadata" class="sidebar-metadata">
					<div id="stats" class="stats"></div>
					<div id="map" class="sidebar-section"></div>
					
				</div>
	  		</div>
		</div>
	</div>

<script type="text/javascript">
	var tree = "<?php echo $tree;?>";
	
	function add_metadata_stat(title,value) {
		$(display_stat(title,value)).appendTo($('#stats'));		
	}
	
	// Display details
	function display_tree_metadata(tree)
	{
		$.getJSON("api/tree/" + tree + '&callback=?',
			function(data){
				if (data.status == 200) {
				
					$('#title').html(tree);
					document.title = tree;
										
					// Taxa
					// We may have multiple sequences form same taxon, so count only distinct
					// taxa
					var taxon_names = [];
					var gi = [];
					var sequence_count = 0;
					
					for (var i in data.translations.taxa) {
						sequence_count++;
						if (taxon_names.indexOf(data.translations.taxa[i]) == -1) {
							taxon_names.push(data.translations.taxa[i]);
							gi.push(i);
						}
					}
					
					// Update how many sequences in tree
					add_metadata_stat('Sequences', sequence_count);
					
					// Display taxa
					var html = '';
					html += '<ul>';
					
					for (var j in gi) {						
						html += '<li>';
						html += '<a href="taxa/ncbi/' + data.translations.tax_id[gi[j]] + '">' + data.translations.taxa[gi[j]] + '</a>';
						html += '</li>';
					}
					html += '</ul>';
					$("#names").html(html);
					
					// Update how many taxa we have
					add_metadata_stat('Taxa', gi.length);
					
					// Set badge on this tab so people know it has something to see
					$('#taxa-badge').text(gi.length);
					// Need this to force tab update
					$('#phylogeny-tabs li:eq(1) a').show();										
				
					// Data sources
					add_metadata_stat('Sources', data.data_sources.length);
					html = '';
					if (data.data_sources) {
					
						// Set badge on this tab so people know it has something to see
						$('#publications-badge').text(data.data_sources.length);
						// Need this to force tab update
						$('#phylogeny-tabs li:eq(2) a').show();
										
						html = '<h3>Publications</h3>';
						html += '<p class="muted">Sources of sequence data</p>';
						html += '<div>';
						for (var i in data.data_sources) {
							//html += '<li>' + display_reference(data.data_sources[i]) + '</li>';
							
							html += '<div style="padding:10px;">';
							html += display_nonlinked_reference(data.data_sources[i]);
							html += '</div>';
							
							// for now these aren't linkable until we figure ou what to do with them
							// maybe local OpenURL
						}
						html += '</div>';
						$("#sources").html(html);
					}
				
					// Map if we have points
					if (data.geometry) {
						html = '<h3>Map</h3>';
						html += '<p class="muted">Localities of sequences</p>';
						html += '<object id="mapsvg" type="image/svg+xml" width="360" height="180" data="map.php?coordinates=' + encodeURIComponent(JSON.stringify(data.geometry.coordinates)) + '"></object>';
						$("#map").html(html);
					}
				}
				
			});
	}
	
			
	// Get NEXUS file
	function display_tree(tree)
	{
		$("#nexus").html("");
		$.get("api/api_tree.php?tree=" + tree + "&format=nexus",
			function(nexus){
				nexus_text = nexus;
				showtree('circlephylogram');
			});
	}
		
		
	function showtree(drawing_type)
	{
		var t = new Tree();	
		nexus = parse_nexus(nexus_text);
		
		if (nexus.status != NexusError.ok)
		{
			html = '<div class="alert alert-error">Error parsing tree</div>';
			$("#message").html(html);
		}
		else
		{
			if (nexus.treesblock.trees.length == 0)
			{
				$("#message").html('<div class="alert alert-error">No trees</div>');
			}
			else
			{
				newick = nexus.treesblock.trees[0].newick;
			
				t.Parse(newick);
			
				if (t.error != 0)
				{
					$("#message").html('<div class="alert alert-error">Error parsing tree</div>');
				}
				else
				{
					$("#message").html('<div class="alert alert-success">Parsed OK (use mouse to zoom and pan)</div>');
					
					t.ComputeWeights(t.root);
					
					var td = null;
					
					switch (drawing_type)
					{
						case 'radial':
							td = new RadialTreeDrawer();
							break;
					
						case 'rectanglecladogram':
							td = new RectangleTreeDrawer();
							break;
					
						case 'phylogram':
							if (t.has_edge_lengths)
							{
								td = new PhylogramTreeDrawer();
							}
							else
							{
								td = new RectangleTreeDrawer();
							}
							break;
							
						case 'circle':
							td = new CircleTreeDrawer();
							break;
							
						case 'circlephylogram':
							if (t.has_edge_lengths)
							{
								td = new CirclePhylogramDrawer();
							}
							else
							{
								td = new CircleTreeDrawer();
							}
							break;
							
						case 'cladogram':
						default:
							td = new TreeDrawer();
							break;
					}
					
					// clear existing diagram, if any
					var svg = document.getElementById('svg');
					while (svg.hasChildNodes()) 
					{
						svg.removeChild(svg.lastChild);
					}
	
					var g = document.createElementNS('http://www.w3.org/2000/svg','g');
					g.setAttribute('id','viewport');
					svg.appendChild(g);				
					
					td.Init(t, {svg_id: 'viewport', width:600, height:600, fontHeight:10, root_length:0.1} );
					
					td.CalcCoordinates();
					td.Draw();
					
					// font size
					var cssStyle = document.createElementNS('http://www.w3.org/2000/svg','style');
					cssStyle.setAttribute('type','text/css');
					
					// rectangular tree
					var font_size = Math.floor(td.settings.height/t.num_leaves);
					font_size = Math.max(font_size, 1);
					
					// circle tree				
					if (td.leaf_angle) {
						
						//font_size = (2 * td.leaf_radius * Math.PI)/ t.num_leaves;
						if (td.leaf_radius) {
							font_size = Math.sin(td.leaf_angle) * td.leaf_radius;
						} else {
							// circular phylogram, radial
							font_size = Math.sin(td.leaf_angle) * td.settings.width/4;
						}
						
						font_size = Math.round(font_size);
						font_size = Math.max(font_size, 1);					
					}
					
					// Tell tree drawer what font size we are using
					td.settings.fontHeight = font_size;
					
					var style=document.createTextNode("text{font-size:" + font_size + "px;}");
					cssStyle.appendChild(style);
					
					svg.appendChild(cssStyle);				
					
					td.DrawLabels(nexus);
								
					// pan
					$('svg').svgPan('viewport');
					
					/*
					// Make SVG downloadable
					// http://stackoverflow.com/questions/8379923/save-svg-image-rendered-by-a-javascript-to-local-disk-as-png-file/8861315#8861315
					// http://stackoverflow.com/a/4228053/9684
					// http://stackoverflow.com/questions/2483919/how-to-save-svg-canvas-to-local-filesystem#comment23679242_4228053
					var svgString = new XMLSerializer().serializeToString(svg);
					var b64 = Base64.encode(svgString);
					$("#download").attr('href', "data:image/svg+xml;base64,\n" + b64);
					$("#download").show();
					*/
					
				}
			}	
		}
	}


	display_tree_metadata(tree);
	display_tree(tree);
	
	// button handlers
	$('.button-radial').click(function(){
	showtree('radial');
	});
	$('.button-cladogram').click(function(){
	showtree('cladogram');
	});
	$('.button-rectanglecladogram').click(function(){
	showtree('rectanglecladogram');
	});
	$('.button-phylogram').click(function(){
	showtree('phylogram');
	});
	$('.button-phylogram').click(function(){
	showtree('phylogram');
	});
	$('.button-circle').click(function(){
	showtree('circle');
	});
	$('.button-circlephylogram').click(function(){
	showtree('circlephylogram');
	});	

<!-- typeahead for search box -->
	$("#q").typeahead({
	  source: function (query, process) {
		$.getJSON('api/name/' + query + '/suggestions?callback=?', 
		function (data) {
		  //data = ['Plecopt', 'Peas'];
		  
		  var suggestions = data.suggestions;
		  process(suggestions)
		})
	  }
	})		

	
</script>

<script>if (use_livereload) { document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>'); }</script>

</body>
</html>
