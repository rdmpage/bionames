<?php

// mockup template

require_once(__DIR__ . '/config.inc.php');

// do PHP stuff here to get query parameters...
if (isset($_GET['tree']))
{
	$tree = $_GET['tree'];
}

?>
<!DOCTYPE html>
<html>
<head>
	<base href="<?php echo $config['web_server'] . $config['web_root'] ?>" /><!--[if IE]></base><![endif]-->
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
	
	<script>
		function openurl(co, id)
		{
			$('#find_' + id).html("Searching...");
			//alert(encodeURIComponent(co, id));
//				$.getJSON("openurl.php?" + co + "&callback=?",
//				$.getJSON("http://biostor-cloud.pagodabox.com/openurl.php?" + co + "&callback=?",
			$.getJSON("bionames-api/openurl.php?" + co + "&callback=?",
				function(data){
					$('#find_' + id).html("Find in BioNames");
					if (data.results.length > 0)
					{
						//alert(data.results[0].id);
						var html = '';
						
						if (data.results.length == 1)
						{
							html += '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>';
							html += '<h4>Found</h4>';
						}
						else
						{
							html += '<div class="alert"><button type="button" class="close" data-dismiss="alert">×</button>';
							html += '<h4>Possible matches</h4>';							
						}
						
						
						html += '<p/>';
						html += '<table>';
						
						for (var i in data.results)
						{							
							html += '<tr>';
							if (data.results[i].reference.thumbnail) {
								html += '<td width="100">' + '<img style="box-shadow:2px 2px 2px #ccc;width:64px;background-color:white;" src="' + data.results[i].reference.thumbnail + '"/>' + '</td>';
							}
							html += '<td>' + '<b>' + data.results[i].reference.title + '</b>' + '<br/>';
							
							html += '<a class="btn btn-primary" href="references/' + data.results[i].reference._id + '">View</a>';
							
							html += '</td>';
							html += '</tr>';
						}
						html += '</table>';
						
						html += '</div>';
						
						$('#reference_' + id).html(html);
					}
					else
					{
						var html = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><h4>Not found</h4>';
						html += '</div>';
						
						$('#reference_' + id).html(html);
					
					}
				}
			);
		}
		
//--------------------------------------------------------------------------------------------------
/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
} 		
	
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
				  <li><a href="#nexus-tab" data-toggle="tab">NEXUS</a></li>
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
									<div style="float:right">
									<a style="display:none;" id="download" href-lang="image/svg+xml" href="" download="tree.svg">
										<img class="toolbarbutton" src="treelib-js/demos/images/download.svg" height="24"/>		
									</a>
									</div>
								</div>
											
								<!-- tree will be drawn here -->
								<svg id="svg" xmlns="http://www.w3.org/2000/svg" version="1.1" height="600" width="600">
									<g id="viewport"></g>
								</svg>
							</div>
						</div>
						
						<div id="message"></div>				  				  

				  </div>
				  
				  <div class="tab-pane" id="nexus-tab">
				  	<textarea id="nexus" style="width:80%;" rows="20" readonly></textarea>
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
					<div id="concepts" class="sidebar-section"></div>
					<div id="map" class="sidebar-section"></div>
					<div id="thumbnails" class="sidebar-section"></div>					
				</div>
				
				<div>
					<?php require 'disqus.inc.php'; ?>
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
					
					// Display how many sequences in tree
					add_metadata_stat('Sequences', sequence_count);
					
					// Display taxa
					var html = '';
					html += '<ul>';
					
					for (var j in gi) {						
						html += '<li>';
						html += '<a href="taxa/ncbi/' + data.translations.tax_id[gi[j]] + '" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'phylogeny\', \'taxon\', 0]);">' + data.translations.taxa[gi[j]] + '</a>';
						html += '</li>';
					}
					html += '</ul>';
					$("#names").html(html);
					
					// Display how many taxa we have
					add_metadata_stat('Taxa', gi.length);
					
					// Set badge on this tab so people know it has something to see
					$('#taxa-badge').text(gi.length);
					// Need this to force tab update
					$('#phylogeny-tabs li:eq(2) a').show();										
				
					// Data sources
					add_metadata_stat('Sources', data.data_sources.length);
					html = '';
					if (data.data_sources) {
					
						// Set badge on this tab so people know it has something to see
						$('#publications-badge').text(data.data_sources.length);
						// Need this to force tab update
						$('#phylogeny-tabs li:eq(3) a').show();
										
						html = '<h3>Publications</h3>';
						html += '<p class="muted">Sources of sequence data</p>';
						html += '<div>';
						for (var i in data.data_sources) {
							//html += '<li>' + display_reference(data.data_sources[i]) + '</li>';
							
							// Reference
							html += '<div style="padding:10px;border-top:1px solid rgb(192,192,192);margin-top:10px;">';
							html += display_nonlinked_reference(data.data_sources[i]);
							html += '</div>';
							
							// Button for local OpenURL lookup for this reference
							html += '<button id="find_' + i + '" class="btn btn-info" onclick="openurl(\''
							 +  referenceToOpenUrl(data.data_sources[i]) + 
							 '\',\'' + i + '\')">Find in BioNames</button>';
							 
							html += '<div id="reference_' + i + '"></div>';
							
						}
						html += '</div>';
						$("#sources").html(html);
					}
					
					// Taxon 
					var concept_id = 'ncbi/' + data.phylota.ti;
					html = '<div id="id' + concept_id.replace(/\//, '_') + '"></div>';
					$('#concepts').html(html);
					display_snippets(concept_id);
					
					// Related trees (now that we know the concept)
					display_related_trees(concept_id);
				
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
				
				$('#nexus').val(nexus_text);
				
				showtree('circlephylogram');
			});
	}
	
	 // Other trees for this concept displayed as thumbnails
      function display_related_trees(concept)
      {
			$("#thumbnails").html("");
			$.getJSON("api/taxon/" + concept + "/trees?format=newick&callback=?",
				function(data){
					if (data.status == 200) {
						if (data.trees.length != 0) {
						
							var html = '';
							html += '<h3>Related trees</h3>';
							
							for (var i in data.trees) {
								html += '<div style="border:1px solid rgb(228,228,228);float:left;margin:10px;background-color:white;">';
								html += '<a href="trees/' + i + '" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'phylogeny\', \'phylogeny\', 0]);">';
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
						
							$("#thumbnails").html(html);
							
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
					
					
					// get unique leaf labels
					var u = get_unique_labels(nexus, t, false);
					
					// Colour scheme from d3js
					// https://github.com/mbostock/d3/wiki/Ordinal-Scales
					var category20  = ['#1f77b4','#aec7e8','#ff7f0e','#ffbb78','#2ca02c','#98df8a','#d62728','#ff9896','#9467bd','#c5b0d5','#8c564b','#c49c94','#e377c2','#f7b6d2','#7f7f7f','#c7c7c7','#bcbd22','#dbdb8d','#17becf','#9edae5'];
					var category20c = ['#3182bd','#6baed6','#9ecae1','#c6dbef','#e6550d','#fd8d3c','#fdae6b','#fdd0a2','#31a354','#74c476','#a1d99b','#c7e9c0','#756bb1','#9e9ac8','#bcbddc','#dadaeb','#636363','#969696','#bdbdbd','#d9d9d9'];
										
					// Get global transform matrix
					gCTM = g.getCTM();
					
					// color stuff
					$( "text" ).each(function( index ) {
											
						// idea from http://srufaculty.sru.edu/david.dailey/svg/getCTM.svg
						SVGRect = this.getBBox();
						
						var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
							rect.setAttribute("x", SVGRect.x);
							rect.setAttribute("y", SVGRect.y);
							rect.setAttribute("width", SVGRect.width);
							rect.setAttribute("height", SVGRect.height);
							
							// Pick a colour
							var index = u.indexOf($(this).text()) % 20;							
							var colour = category20[index];
							
							rect.setAttribute("fill", colour);
							rect.setAttribute("opacity", 0.5);
							
							CTM=this.getCTM();
							s=CTM.a+" "+CTM.b+" "+CTM.c+" "+CTM.d+" "+CTM.e+" "+CTM.f;
							rect.setAttributeNS(null,"transform","translate("+ -gCTM.e + "," + -gCTM.f + "),matrix("+s+")");
							
							g.insertBefore(rect, this);						
					});
								
					// pan
					$('svg').svgPan('viewport');
					
					
					// Make SVG downloadable
					// http://stackoverflow.com/questions/8379923/save-svg-image-rendered-by-a-javascript-to-local-disk-as-png-file/8861315#8861315
					// http://stackoverflow.com/a/4228053/9684
					// http://stackoverflow.com/questions/2483919/how-to-save-svg-canvas-to-local-filesystem#comment23679242_4228053
					var svgString = new XMLSerializer().serializeToString(svg);
					var b64 = Base64.encode(svgString);
					$("#download").attr('href', "data:image/svg+xml;base64,\n" + b64);
					$("#download").show();
					
					
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
