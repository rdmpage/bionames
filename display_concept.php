<?php

require_once('bionames-api/lib.php');

// do PHP stuff here to get query parameters...
$id = $_GET['id'];

// OK, we need some HTML content that Google can see when it crawls the page...
$json = get('http://bionames.org/api/id/' . $id);

$doc = json_decode($json);

if(isset($doc->scientificName))
{
	$title = $doc->scientificName;
}
else
{
	$title = $doc->canonicalName;
}
?>
<!DOCTYPE html>
<html>
<head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
	<title><?php echo $title; ?></title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
	<script src="treelib-js/treelib.js"></script>   
	<script src="js/publication.js"></script>  
	
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?sensor=false">
    </script>
    
    <script type="text/javascript">    
		var map; // Google map
	
		
		var gbifTypeOptions = {
		  getTileUrl: function(coord, zoom) {
			  var normalizedCoord = getNormalizedCoord(coord, zoom);
			  if (!normalizedCoord) {
				return null;
			  }
			  var bound = Math.pow(2, zoom);
		
				// GBIF custom tiles    
			/*
			  return "http://a.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/69341/256" +
				  "/" + zoom + "/" + normalizedCoord.x + "/" +
				  normalizedCoord.y + ".png";
			*/  
			  return "http://c.tiles.mapbox.com/v3/timrobertson100.map-x2mlizjd" +
				  "/" + zoom + "/" + normalizedCoord.x + "/" +
				  normalizedCoord.y + ".png";
			
			
				// GBIF API
//				return "http://api.gbif.org/v0.9/map/density/tile?x=" + zoom + "&y=" + normalizedCoord.x + "&z=" + normalizedCoord.y;
//				return "http://api.gbif.org/v0.9/map/density/tile?x=" + normalizedCoord.x + "&y=" + normalizedCoord.y + "&z=" + zoom;
//				return "http://api.gbif.org/v0.9/map/density/tile?x=" + normalizedCoord.x + "&y=" + normalizedCoord.y + "&z=" + zoom + "&type=COUNTRY" + "&key=" + "NZ";
		  },
		  tileSize: new google.maps.Size(256, 256),
		  maxZoom: 9,
		  minZoom: 0,
		  radius: 1738000,
		  name: "GBIF"
		};
		
		// Normalizes the coords that tiles repeat across the x axis (horizontally)
		// like the standard Google map tiles.
		function getNormalizedCoord(coord, zoom) {
		  var y = coord.y;
		  var x = coord.x;
		
		  // tile range in one direction range is dependent on zoom level
		  // 0 = 1 tile, 1 = 2 tiles, 2 = 4 tiles, 3 = 8 tiles, etc
		  var tileRange = 1 << zoom;
		
		  // don't repeat across y-axis (vertically)
		  if (y < 0 || y >= tileRange) {
			return null;
		  }
		
		  // repeat across x-axis
		  if (x < 0 || x >= tileRange) {
			x = (x % tileRange + tileRange) % tileRange;
		  }
		
		  return {
			x: x,
			y: y
		  };
		}
		
		var gbifMapType = new google.maps.ImageMapType(gbifTypeOptions);
		
		var bounds = new google.maps.LatLngBounds();		
     
		function initialize() {
		
			// Make Map
<?php
	if (isset($doc->geometry))
	{
		echo 'var geometry = ' . json_encode($doc->geometry) . ';';
	}
	else
	{
		echo 'var geometry = null;';
	}
	echo "\n";
?>
			if (geometry)
			{
				$('#concept-tabs li:eq(2) a').html('Map');
			 
				var myOptions = {
					center: new google.maps.LatLng(0, 0),
					zoom: 2,
					streetViewControl: false,
					mapTypeId: 'GBIF',
					//mapTypeId: google.maps.MapTypeId.TERRAIN
					mapTypeControlOptions: {
						mapTypeIds: ['GBIF', google.maps.MapTypeId.TERRAIN],
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
						}
					};
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				
				// Now attach the coordinate map type to the map's registry
				map.mapTypes.set('GBIF', gbifMapType);
				
				
				for (var i in geometry.coordinates)
				{
				   var square = [
					new google.maps.LatLng(geometry.coordinates[i][0][0][1], geometry.coordinates[i][0][0][0]),
					new google.maps.LatLng(geometry.coordinates[i][0][1][1], geometry.coordinates[i][0][1][0]),
					new google.maps.LatLng(geometry.coordinates[i][0][2][1], geometry.coordinates[i][0][2][0]),
					new google.maps.LatLng(geometry.coordinates[i][0][3][1], geometry.coordinates[i][0][3][0]),
					new google.maps.LatLng(geometry.coordinates[i][0][4][1], geometry.coordinates[i][0][4][0])
					];
								
					bounds.extend(square[0]);
					bounds.extend(square[2]);
					
					var polygon = new google.maps.Polygon({
						paths: square,
						strokeColor: "#FFFF00",
						strokeOpacity: 0.8,
						strokeWeight: 1.0,
						fillColor: "#FFFF00",
						fillOpacity: 0.8
						});
					polygon.setMap(map);
				}
				//map.fitBounds(bounds); 
			}
		  }
	</script>    
	
	
	
</head>
<body class="concept" onload="initialize()">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>
	
<div itemscope itemtype="http://schema.org/Thing" class="container-fluid">
	<div class="row-fluid">
  		<div class="span8">
			<ul id="concept-tabs" class="nav nav-tabs">
			  <li class="active" ><a href="#name-tab" data-toggle="tab">Name <span id="name-badge" class="badge badge-info"></span></a></li>
			  <li class="bibliography"><a href="#biblio-tab" data-toggle="tab">Bibliography <span id="bibliography-badge"class="badge badge-info"></span></a></li>
			  <li><a href="#data-tab" data-toggle="tab">Data <span id="data-badge"class="badge badge-info"></span></a></li>
			  <li><a href="#about-tab" data-toggle="tab">About</a></li>
			</ul>
			
			<div class="tab-content">
			  <div class="tab-pane active" id="name-tab">
<?php
if (isset($doc->identifier))
{
	$num_names = 0;
	
	if (isset($doc->identifier->ion))
	{
		$publications = array();
		
		foreach ($doc->identifier->ion as $k => $name) 
		{
			echo '<div>';

			echo '<a href="names/cluster/' .  $k . '">';
			echo  $name->nameComplete;
			echo '</a>';
			
			// publication
			if (isset($name->publishedInCitation)) 
			{									
				echo '<div id="id' . $name->publishedInCitation[0] . '">' . $name->publishedInCitation[0] . '</div>';
				$publications[] = $name->publishedInCitation[0];
			} else 
			{
				if (isset($name->publication)) 
				{
					echo '<div>' . $name->publication[0] . '</div>';
				}
			}
			echo '</div>';
			
			$num_names++;
		}
		foreach ($publications as $id)
		{
			echo '<script>display_publications("' . $id . '");</script>';
		}
	}
}
?>
				</div>
			  
			  <div class="tab-pane no-pad" id="biblio-tab">
				<div id="publication-timeline" class="publication-timeline">
					<div class="pub-timeline">
					    <table id="synonymTimeline">
					        <thead><tr><td></td><td><div class="axis top"></div></td></tr></thead>
					        <tbody id="nameTimelines"></tbody>
					        <tfoot><tr><td></td><td><div class="axis bottom"></div></td></tr></thead>
					    </table>
					</div>
				    <div id="pubList"></div>
				</div>
			  </div>
			  
			  <div class="tab-pane" id="data-tab">
			  	<div id="data">
			  		<div id="map_canvas" style="width:100%; height:400px;"></div>
			  	</div>
			  </div>
			  
			  <div class="tab-pane" id="about-tab">...</div>
			</div>			
			
		</div>
		
  		<div class="sidebar span4">
			<div class="sidebar-header">
				<h1 id="title"><span itemprop="name"><?php echo $title; ?></span></h1>
<?php
				// TDWG vocabulary
				echo '<span vocab="http://rs.tdwg.org/ontology/voc/" typeof="TaxonConcept" >';
				echo '<meta property="nameString" content="' . $title . '" />';
				if (isset($doc->taxonRank))
				{
					echo '<meta property="rankString" content="' . $doc->taxonRank . '" />';
				}
				if (isset($doc->identifier->ion))
				{
					foreach ($doc->identifier->ion as $k => $name) 
					{
						echo '<meta property="hasName" content="http://bionames.org/names/cluster/' . $k . '" />';
					}
				}
				echo '</span>';
?>
			</div>
			<div id="metadata" class="sidebar-metadata">
				<div id="stats" class="stats">
					<div class="metadatum">
						<div class="metadatum-title source">Source</div>
						<div class="metadatum-value">
<?php
						$sourcePrefix = array();
						$sourcePrefix['http://ecat-dev.gbif.org/checklist/1'] = 'gbif';
						$sourcePrefix['http://www.ncbi.nlm.nih.gov/taxonomy'] = 'ncbi';

						switch ($sourcePrefix[$doc->source])
						{
							case 'gbif':
								echo '<span itemscope itemtype="http://schema.org/Organization">';
								echo '<meta itemprop="name" content="GBIF" />';
								echo '<meta itemprop="url" content="http://www.gbif.org" />';
								echo '<img src="images/logo-gbif-stats.png" id="logo" />';
								echo '</span>';
								break;
								
							case 'ncbi':
								echo '<span itemscope itemtype="http://schema.org/Organization">';
								echo '<meta itemprop="name" content="NCBI" />';
								echo '<meta itemprop="url" content="http://www.ncbi.nlm.nih.gov" />';
								echo '<img src="images/logo-ncbi-stats.png" id="logo" />';
								echo '</span>';							
								break;
								
							default:
								break;
						}
?>
						</div>
					</div>
					<div class="metadatum">
						<div class="metadatum-value">
							<div class="metadatum-title rank">Rank</div>
							<div class="metadatum-value"><?php echo $doc->taxonRank; ?></div>
						</div>
					</div>
<?php
					$num_names = 0;
					if (isset($doc->identifier))
					{
						if (isset($doc->identifier->ion))
						{
							$num_names += count($doc->identifier->ion);
							
							echo '<div class="metadatum">';
							echo '	<div class="metadatum-value">';
							echo '		<div class="metadatum-title rank">Names</div>';
							echo '		<div class="metadatum-value">' . $num_names . '</div>';
							echo '	</div>';
							echo '</div>';
						}
					}
?>
					
				</div>
			</div>
			<div id="sourcelink" class="sidebar-section">
				<div>
<?php
				switch ($sourcePrefix[$doc->source])
				{
					case 'gbif':
						echo '<a href="http://www.gbif.org/species/' . $doc->sourceIdentifier . '" target="_new" onclick="_gaq.push([\'_trackEvent\', \'External\', \'gbif\', $doc->sourceIdentifier, 0]);" rel="tooltip" title="" class="tip" data-original-title="GBIF taxon concept">';
						echo '<i class="icon-share"></i> species:' . $doc->sourceIdentifier . '</a>';
						
						if (isset($doc->namePublishedIn))
						{
							echo '<div>' . $doc->namePublishedIn . '</div>';
						}
											
						break;
						
					case 'ncbi':
						echo '<a href="http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=' . $doc->sourceIdentifier . '" target="_new" onclick="_gaq.push([\'_trackEvent\', \'External\', \'ncbi\', $doc->sourceIdentifier, 0]);" rel="tooltip" title="" class="tip" data-original-title="NCBI taxon concept">';
						echo '<i class="icon-share"></i> taxonomy:' . $doc->sourceIdentifier . '</a>';					
						break;
						
					default:
						break;
				}
?>
				</div>
			</div>
			<div id="images" class="sidebar-section"></div>
			<div id="classification" class="sidebar-section">
<?php												
						// Classification (nodes immediately above and below)
						echo '<h3>Classification</h3>';
							
						echo '<ul class="classification">' . "\n";
						
						// Parent taxon
						echo '<li class="root">' . "\n";
						if (isset($doc->ancestors))
						{
							echo '<a href="taxa/' . $sourcePrefix[$doc->source] . '/' . $doc->ancestors[count($doc->ancestors)-1]->sourceIdentifier . '">';
							echo '<p style="line-height:16px;padding:0px;margin:0px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:100%;opacity:0.6">' . $doc->ancestors[count($doc->ancestors)-1]->scientificName . '</p>';
							echo '</a>';
						}
						
						// Taxon
						echo '<ul class="classification">';
						echo '<li class="lastchild">'; 
						echo '<span>' . $doc->scientificName . '</span>';
													
						// Children
						if (isset($doc->children))
						{
							echo '<ul class="classification">';
							$num_children = count($doc->children);
							for ($j = 0; $j < $num_children; $j++)
							{
								if ($j == ($num_children - 1))
								{
									echo '<li class="lastchild">';
								}
								else
								{
									echo '<li class="child">';
								}
								echo '<a href="taxa/' . $sourcePrefix[$doc->source] . '/' .  $doc->children[$j]->sourceIdentifier . '">';
								echo '<p style="line-height:16px;padding:0px;margin:0px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:100%;opacity:0.6">' . $doc->children[$j]->scientificName . '</p>';
								echo '</a>';
								echo '</li>';
							}
							echo '</ul>';
						}

						echo '</li>' . "\n"; // taxon
						echo '</ul>' . "\n";
						
						echo '</li>' . "\n"; // root
						echo '</ul>' . "\n";							
?>
			</div>
			
				<div>
					<?php require 'disqus.inc.php'; ?>
    			</div>
			
  		</div>
	</div>
</div>

	<script src="js/display.js"></script>
	<script src="js/openurl.js"></script>
	<script src="js/publication.js"></script>

	<script>
		var concept = "<?php echo $doc->_id;?>";
		show_images(concept);
		show_publications(concept);		
		show_trees(concept);
		
		function add_metadata_stat(title,value) {
			$(display_stat(title,value)).appendTo($('#stats'));		
		}
	 
	  // Other trees for this concept displayed as thumbnails
      function show_trees(concept)
      {
			//$("#data").html("");
			$.getJSON("api/taxon/" + concept + "/trees?format=newick&callback=?",
				function(data){
					if (data.status == 200) {
						if (data.trees.length != 0) {
						
							var html = '';
							html += '<div style="background-color:#fafafa;">';
							
							var num_trees = 0;
							
							for (var i in data.trees) {
								num_trees++;
								html += '<div style="border:1px solid rgb(228,228,228);float:left;margin:10px;background-color:white;">';
								html += '<a href="trees/' + i + '">';
								html += '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="88" width="88">';
								html += '<g id="' + i.replace(/\//, '_') + '"></g>'; 								
								html += '</svg>';		
								html += '</a>';								
								if (data.tags[i]) {
									//html += '<div style="width:88px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + data.tags[i].join() + '</div>';
									html += '<div style="width:88px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + data.tags[i][0] + '</div>';
								}
								html += '</div>';
							}
							html += '<div style="clear:both;"/>';
							html += '</div>';
							$("#data").html(html);
							
							// Set badge on this tab so people know it has something to see	
							$('#concept-tabs li:eq(2) a').html('Phylogeny <span id="data-badge"class="badge badge-info">');
							
							$('#data-badge').text(num_trees);
							// Need this to force tab update
							$('#concept-tabs li:eq(2) a').show();

							// draw trees
							for (var i in data.trees) {
								var t = new Tree();
								t.Parse(data.trees[i]);
	
								if (t.error != 0)
								{
								}
								else
								{							
									t.ComputeWeights(t.root);		
									var td = new CirclePhylogramDrawer();
									td.Init(t, {svg_id: i.replace(/\//, '_'), width:88, height:88, fontHeight:0, root_length:0.1} );		
									td.CalcCoordinates();
									td.Draw();
								}
							}	
						}
					}
				});
	 }		
		
      function show_images(concept)
      {
			$("#images").html("");
			$.getJSON("api/taxon/" + concept + "/thumbnail?callback=?",
				function(data){
					if (data.status == 200) {
						if (data.thumbnails.length != 0) {
							var html = '';
							html += '<h3>Images</h3>';
							html += '<div class="image-gallery">';
							var n = Math.min(8, data.thumbnails.length);
							for (var i=0;i<n;i++) {
								html += '<img src="' + data.thumbnails[i] + '" />';
							}
							html += '</div>';
							html += '<div>Images from <a href="http://eol.org/pages/' + data.eol + '">EOL</a></div>';
							
						
							$("#images").html(html);
						}
					}
				});
	 }		
	 
		function show_classification(concept)
		{
			$("#classification").html("");
			$("#namemap").html("");
			
			$.getJSON("api/taxon/" + concept + "?callback=?",
				function(data){
					if (data.status == 200)
					{		
						
						var html = '';
						
						$("#title").html(data.scientificName);
						document.title = data.scientificName;
						
						add_metadata_stat("Rank", data.taxonRank);
						
						var sourcePrefix = [];
						sourcePrefix['http://ecat-dev.gbif.org/checklist/1'] = 'gbif';
						sourcePrefix['http://www.ncbi.nlm.nih.gov/taxonomy'] = 'ncbi';
						
						// logo
						switch (sourcePrefix[data.source])
						{
							case 'gbif':
								$('#logo').attr('src', 'images/logo-gbif-stats.png').show();
								break;
								
							case 'ncbi':
								$('#logo').attr('src','images/logo-ncbi-stats.png').show();
								break;
								
							default:
								break;
						}
						
						// Classification (nodes immediately above and below)
						html += '<h3>Classification</h3>';
							
						html += '<ul class="classification">';
						
						// Parent taxon
						html += '<li class="root">';
						if (data.ancestors)
						{
							html += '<a href="taxa/' + sourcePrefix[data.source] + '/' + data.ancestors[data.ancestors.length-1].sourceIdentifier + '">' + '<p style="line-height:16px;padding:0px;margin:0px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:100%;opacity:0.6">' + data.ancestors[data.ancestors.length-1].scientificName + '</p>' + '</a>';
						}
						
						// This taxon
						html += '<ul class="classification">';
						html += '<li class="lastchild">' 
							//+ '<a href="?id=' + sourcePrefix[data.source] + '/' +  data.sourceIdentifier + '">'
//							+ '<span style="background-color:yellow">' + data.scientificName + '</span>'
							+ '<span>' + data.scientificName + '</span>'
							//+ '</a>';						
						
						// Child taxa
						if (data.children)
						{
							
							add_metadata_stat("children", data.children.length);
							html += '<ul class="classification">';
							var num_children = data.children.length;
							for (j = 0; j < num_children; j++)
							{
								if (j == (num_children - 1))
								{
									html += '<li class="lastchild">';
								}
								else
								{
									html += '<li class="child">';
								}
								html += '<a href="taxa/' + sourcePrefix[data.source] + '/' +  data.children[j].sourceIdentifier + '">' + '<p style="line-height:16px;padding:0px;margin:0px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:100%;opacity:0.6">' + data.children[j].scientificName + '</p>' + '</a>' + '</li>';
							}
							html += '</ul>';
						}
						html += '</li>'
						html += '</ul>'; // this taxon				
						
						html += '</li>'; // root
						html += '</ul>';
						
						// Classification
						$("#classification").html(html);
						
						// Classification-specific info (e.g., maps, phylogeny
						/*
						html = '<img src="http://data.gbif.org/species/' + data.sourceIdentifier + '/overviewMap.png" width="360"/>';
						$("#info").html(html);
						*/
						/*
						switch (sourcePrefix[data.source])
						{
							case 'gbif':
								//gbif_map(data.sourceIdentifier);
								break;
								
							case 'ncbi':
								//$('#map_canvas').hide();
								break;
								
							default:
								break;
						}
						*/
						
						// Accepted name

						$("#title").html(data.scientificName);
						
						if (data.namePublishedIn) {
							$("#namePublishedIn").html(data.namePublishedIn);
						}
						
						// Thumbnail
						
						// Taxon names for this concept
						if (data.identifier)
						{
							var num_names = 0;
							
							if (data.identifier.ion)
							{
								var publications = [];
								
								var html = '';
								for (var j in data.identifier.ion) {
									html += '<div>';
									
									html += '<a href="names/cluster/' + j + '">';
									html += data.identifier.ion[j].nameComplete;
									html += '</a>';
									
									// publication
									if (data.identifier.ion[j].publishedInCitation) {									
										html += '<div id="id' + data.identifier.ion[j].publishedInCitation[0] + '">' + data.identifier.ion[j].publishedInCitation[0] + '</div>';
										publications.push(data.identifier.ion[j].publishedInCitation[0]);
									} else {
										if (data.identifier.ion[j].publication) {
											html += '<div>' + data.identifier.ion[j].publication[0] + '</div>';
										}
									}
									html += '</div>';
									
									num_names++;
								}
							 	
						  
							 if(num_names > 0) {
							 	add_metadata_stat("Names", num_names);
							 	
							 	// Set badge on this tab so people know it has something to see
								$('#name-badge').text(num_names);
								// Need this to force tab update
								$('#concept-tabs li:eq(0) a').show();
							}

							 
							 
							 
						// Publications
						for (var pubs in publications) {
							html += '<script>display_publications_ryan("' + publications[pubs] + '");<\/script>';
						}
							 
							 
							 $("#name-tab").html(html);
						   }
						}
						
					}
				});
		} 
		
		
		function show_publications(concept) {
			$.getJSON("api/taxon/" + concept + "/publications?fields=title,thumbnail,identifier,author,journal,year&include_docs&callback=?", function(data){
			
				add_metadata_stat("Publications", data.publications.length);
				
				if( data.publications.length == 0 ) return;
				
				// Set badge on this tab so people know it has something to see
				$('#bibliography-badge').text(data.publications.length);
				// Need this to force tab update
				$('#concept-tabs li:eq(1) a').show();
				
				var publication_list = [];
    
			    // Clean up and type cast the JSON objects where necessary.
			    for(var i in data.publications) {
			        var pub = data.publications[i];
        
			        pub.year = +pub.year; // Cast to integer
			        pub.tags = pub.tags.sort();
        
        
			        // For each tag, we need to create a new object. Hacky I know, but 
			        // Crossfilter doesn't handle dimensions where an object can have multiple values
			        for(var t = 0; t < pub.tags.length; t++) {
			            var newPub = {};
            
			            for(var prop in pub) {
			                newPub[prop] = pub[prop];
			            }
            
			            newPub.tag  = pub.tags[t];            
			            publication_list.push(newPub);
			        }
			    }
    

			    // Crossfilter
			    var pubs = crossfilter( publication_list );
    
			    // Dimensions and groups
			    var pubsByYear = pubs.dimension( function(d){ return d.year; } );
			    var years = pubsByYear.group();
			    var pubsByName = pubs.dimension( function(d){ return d.tag; } );
			    var names = pubsByName.group();
    
    
			    // Nest for grouping publication list by decade
			    var nestByDecade = d3.nest().key(function(d){ return Math.floor(d.year/10) * 10; });   
    
			    // Used to set the maximum domain of the y-axis of timelines
			    countMax = 0;
			    names.all().forEach(function(n){
			        var name = n.key,
			            maxForName;
            
			        pubsByName.filter(name);
			        maxForName = d3.max(years.all(), function(v){ return v.value});
			        countMax = d3.max([countMax, maxForName]);
			    });
			    pubsByName.filterAll();
    
			    // Used in the x-axis of timelines
			    var yearExtent = d3.extent( years.all(), function(d){ return d.key; } );
    
			    // Crossfilter unfortunately does not support union filters, so we have to kind of build our own
			    // This set will keep track of which names have been selected with the checkboxes,
			    // and the unionNames function is used later to filter pubsByName by the selectedNames
			    var selectedNames = d3.set( names.all().map(function(d){ return d.key; }) );
			    var unionNames = function(d){ return selectedNames.has(d); };
    
			    var xScale = d3.scale.linear()
			            .domain([ yearExtent[0], yearExtent[1]+1])
			            .rangeRound([0, 600]);
			    var yScale = d3.scale.linear().domain([0, countMax]).rangeRound([0, 30]);
    
			    var nameTimeline = d3.select("#nameTimelines").selectAll(".nameTimeline")
			        .data( names.all().map(function(d){ return d.key; }) ) 
			        .enter().append('tr')
			            .attr('class', 'nameTimeline')
    
			    var nameLabel = nameTimeline.append('td').append('label')
			    nameLabel.append('input')
			        .attr({type: 'checkbox', checked: 'checked' })
			        .on('change', function(d){
			            d3.select(this.parentNode.parentNode.parentNode).classed("disabled", !this.checked);
			            this.checked ? selectedNames.add(d) : selectedNames.remove(d);
			            pubsByName.filter(unionNames);
			            renderAll();
			        });
			    nameLabel.append('span').text(function(d){ return d; });
    
			    nameTimeline.append('td').append('div').attr("class", "chart")
    
			    var charts = names.all().map(function(n){
			        var name = n.key;
			        return (filterWidgets.histogram()
			            .dimension(pubsByYear)
			            .group(years)
			            .beforeDraw( function(){ pubsByName.filter(name); })
			            .afterDraw( function(){ pubsByName.filterAll(); })
			            .round( Math.round )
			            .xScale( xScale )
			            .yScale( yScale )
			            .margin({ top: 5, right: 12, bottom: 0, left: 12 })
			        );
			    });
    
			    var axes = [
			        filterWidgets.axis()
			            .xScale( xScale )
			            .orient('top'),
			        filterWidgets.axis()
			            .xScale( xScale )
			            .orient('bottom')
			    ];
    
			    var lists = [
			        filterWidgets.publicationList().dimension(pubsByYear).nest(nestByDecade)
			    ];
    
    
			    var chart = d3.selectAll(".nameTimeline .chart")
			        .data(charts)
			        .each(function(chart) {
			             chart.on("brush", function(c){
			                 charts.forEach(function(chrt){ chrt.filter(c.brush().extent())});
			                 renderAll()
			             }).on("brushend", renderAll); });
    
			    var axis = d3.selectAll(".axis")
			        .data(axes);
    
			    var list = d3.selectAll("#pubList")
			        .data(lists);

			    function renderAll(){
			        list.each(render);
			        axis.each(render);
			        chart.each(render);
			    }

			    function render( method ) {
			        d3.select(this).call(method);
			    }

			    renderAll();
			});
			
		}
		
	// If we click on maps tab, resize map otherwise we get only part of window filled with map
	// http://stackoverflow.com/questions/6455536/google-maps-api-v3-jquery-ui-tabs-map-not-resizing
	$('a[data-toggle="tab"]').on('shown', function (e) {
  
  	
  		var t = $(e.target).text().toLowerCase();
  		switch (t)
  		{
  			case 'map':
   				google.maps.event.trigger(map, 'resize');
  				map.fitBounds(bounds); 
  				break;
  				
  			default:
  				break;
  		}
	})	
		
	$('.tip').tooltip();

<!-- typeahead for search box -->
	$("#q").typeahead({
	  source: function (query, process) {
		//$.getJSON('http://bionames.org/bionames-api/name/' + query + '/suggestions?callback=?', 
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