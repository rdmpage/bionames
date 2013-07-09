<?php

require_once('bionames-api/lib.php');

// do PHP stuff here to get query parameters...
$id = $_GET['id'];

// OK, we need some HTML content that Google can see when it crawls the page...
$json = get('http://bionames.org/api/id/' . $id);

$doc = json_decode($json);

$title = $doc->nameComplete;
if (isset($doc->taxonAuthor))
{
	$title .= ' ' . $doc->taxonAuthor;
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

	<script>	
	
		function add_metadata_stat(title,value) {
			$(display_stat(title,value)).appendTo($('#stats'));		
		}	
	</script>
</head>
<body class="name">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>

	<div itemscope itemtype="http://schema.org/Thing" class="container-fluid">
	  <div class="row-fluid">
	    <div class="main-content span8">
			<ul id="names-tabs" class="nav nav-tabs">
			  <li class="active"><a href="#names-tab" data-toggle="tab">Names <span id="names-badge" class="badge badge-info"></span></a></li>
			  <li class="bibliography"><a href="#biblio-tab" data-toggle="tab">Bibliography <span id="bibliography-badge" class="badge badge-info"></span></a></li>
			  <li><a href="#species-tab" data-toggle="tab">Species <span id="species-badge" class="badge badge-info"></span></a></li>
			</ul>
			
			<div class="tab-content">
			  <div class="tab-pane active" id="names-tab">
				<div id="names">
					<h4>Name(s) in cluster</h4>
					<table class="table">
<?php				
for ($i=0; $i < count($doc->names);$i++) 
{
	echo '<tr>' . "\n";
	echo '<td>';
	echo $doc->names[$i]->nameComplete;
	if ($doc->names[$i]->taxonAuthor) {
		echo ' ' . $doc->names[$i]->taxonAuthor;
	}
	echo '</td>' . "\n";
	echo '<td>';
	if (preg_match('/urn:lsid:organismnames.com:name:/', $doc->names[$i]->id))
	{
		echo '<a href="http://www.organismnames.com/details.htm?lsid=' . str_replace('urn:lsid:organismnames.com:name:', '', $doc->names[$i]->id) . '" target="_new" onClick="_gaq.push([\'_trackEvent\', \'External\', \'lsid\', \'' . $doc->names[$i]->id . '\', 0]);" rel="tooltip" title="Life Science Identifier (LSID) for this taxon name" class="tip"><i class="icon-share"></i> ' . $doc->names[$i]->id . '</a>';
	}		
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
}
?>		
					</table>
				</div>
			  </div>
			
			  <div class="tab-pane no-pad" id="biblio-tab">
		        <div id="publication-timeline" class="publication-timeline">
					<div class="pub-timeline">
						<div id="pubHistogram" class="chart"></div>
					</div>
		            <div id="pubList"></div>
		        </div>
			  </div>
			  
			  <div class="tab-pane" id="species-tab">
				<div id="species">...</div>
			  </div>
			  
			  
		  </div>
	    </div>
	    <div class="sidebar span4">
			<div class="sidebar-header">
				<h1 id="title">
					<span itemprop="name"><?php echo $title; ?></span>
				</h1>
			</div>
			<div id="metadata" class="sidebar-metadata">
				<div id="stats" class="stats"></div>
				<div id="publishedin">
<?php
foreach ($doc->names as $name)
{
	if (isset($name->publication))
	{
		if (isset($name->publishedInCitation))
		{
			echo '<div id="publication' . $name->publishedInCitation . '">';		
			echo '<span itemscope itemtype="http://schema.org/CreativeWork">';
			echo '<meta itemprop="url" content="' . 'http://bionames.org/references/' . $name->publishedInCitation . '" />';
			echo '</span>';
		}	
		else
		{
			echo '<div>';
		}
		echo $name->publication;
		echo '</div>';
	}
}
?>				
				
				</div>
			</div>
	    	<div id="concepts" class="sidebar-section"></div>
	    	<div id="related" class="sidebar-section"></div>
	    	<div id="epithet" class="sidebar-section"></div>
	    	
			<div>
				<?php require 'disqus.inc.php'; ?>
			</div> 	
	    </div>
	  </div>
	</div>

<script>
	<?php echo 'var id = "' . $doc->_id . '";'; ?>

	/* tooltips */
	$('.tip').tooltip();
	
<?php 
echo '	add_metadata_stat(\'Names\', ' . count($doc->names) . ');' . "\n";	
echo '	$(\'#names-badge\').text(' . count($doc->names) . ');' . "\n";
?>	
	$('#names-tabs li:eq(0) a').show();
	
	function show_concepts(id)
	{
		$("#concepts").html("");
		
		$.getJSON("api/name/id/" + id + "/concepts?callback=?",
			function(data){
				if (data.status == 200)
				{							
					add_metadata_stat("Concepts", data.concepts.length);
					
					if (data.concepts.length > 0) {
						var concepts = [];
						var html = '<h3>This name has been used for these taxa</h3>';
						for (var i in data.concepts) {
							html += '<div id="id' + data.concepts[i].concept.replace('/', '_') + '"></div>';
							concepts.push(data.concepts[i].concept);
						}
						$("#concepts").html(html);
						
						for (var i in concepts) {
							display_snippets(concepts[i]);
						}
					}
				}
			});
	}
	
		function show_epithet(epithet)
		{
			$("#epithet").html("");
			
			$.getJSON("api/name/" + encodeURIComponent(epithet) + "/epithet?callback=?",
				function(data){
					if (data.status == 200)
					{	
						if (data.names.length > 1) {					
							var html = '<h3>Names with same epithet</h3>';
							html += '<div>';
							for (var i in data.names)
							{
								var s = data.names[i];
								html += '<a href="search/' + encodeURIComponent(s) + '">' + s + '</a>' + '<br />';
							}
							html += '</div>';
							var current_html = $("#epithet").html();
							$("#epithet").html(current_html + html);
						}
					}
				});
		}
		
		function show_related(name)
		{
			$("#related").html("");
			
			$.getJSON("api/name/" + encodeURIComponent(name) + "/related?callback=?",
				function(data){
					if (data.status == 200)
					{	
						add_metadata_stat("Related Names", data.related.length);
						
						if (data.related.length > 0) {					
							var html = '<h3>Related names</h3>';
							html += '<p class="muted">Possible synonyms</p>';
							html += '<ul>';
							for (var i in data.related)
							{
								var s = data.related[i];
								html += '<li><a href="search/' + encodeURIComponent(s) + '">' + s + '</a></li>';
							}
							html += '</ul>';
							var current_html = $("#related").html();
							$("#related").html(current_html + html);
						}
					}
				});
		}
		
		function show_publications(name)
		{		
			$.getJSON("api/name/" + encodeURIComponent(name) + "/publications?fields=title,thumbnail,identifier,author,journal,year&include_docs" + "&callback=?",
				function(data){
					if (data.status == 200)
					{								
						add_metadata_stat("Publications", data.publications.length);
						
						// Set badge on this tab so people know it has something to see
						$('#bibliography-badge').text(data.publications.length);
						// Need this to force tab update
						$('#names-tabs li:eq(1) a').show();
						
	                    // Type cast years into integers
						for (var i in data.publications)
						{
	                        data.publications[i].year = +data.publications[i].year;
						}
                                            
	                    // Crossfilter, dimensions, and groups
	                    var publication = crossfilter(data.publications),
	                        year = publication.dimension(function(d){ return d.year; }),
	                        years = year.group();

	                    var yearsExtent = d3.extent( years.all(), function(d){ return d.key; })

	                    // Nest operator for grouping the list by decade
	                    var nestByDecade = d3.nest()
	                        .key(function(d){ return Math.floor(d.year/10) * 10; });
                        
                        
	                        // Charts
	                        var histograms = [
	                            filterWidgets.histogram()
	                                .dimension(year)
	                                .group(years)
	                                .round( Math.round )
	                                .xScale( d3.scale.linear()
	                                    .domain([ yearsExtent[0], yearsExtent[1]+1])
	                                    .rangeRound([0, 400])
	                                    .nice())
	                                .yScale( d3.scale.linear().rangeRound([0, 60]) )
	                        ];

	                        var lists = [
	                            filterWidgets.publicationList().dimension(year).nest(nestByDecade)
	                        ];
                        
                        
	                        // Given an array of histogram definitions, bind them to
	                        // charts in the DOM, which we assume are in the same order
	                        var chart = d3.selectAll(".chart")
	                            .data(histograms)
	                            .each(function(c){ c.on("brush", renderAll).on("brushend", renderAll); })

	                        var list = d3.selectAll("#pubList")
	                            .data(lists);

	                        function renderAll(){
	                            chart.each(render);
	                            list.each(render);
	                        }

	                        function render( method ) {
	                            d3.select(this).call(method);
	                        }

	                        renderAll();
					}
				});
		}
		
		function show_published_in(publishedInCitation)
		{
			for (var id in publishedInCitation)
			{
				$.getJSON("api/id/" + publishedInCitation[id] + "?callback=?",
					function(data){
						if (data.status == 200) {
							show_snippet('publication' + publishedInCitation[id], data);
						}
					});
			}
				
		}	
		
		function show_species(name)
		{
			$("#species").html("");
			
			$.getJSON("api/name/" + encodeURIComponent(name) + "/species?callback=?",
				function(data){
					if (data.status == 200) {
						
						if (data.species.length > 0) {	
							// Set badge on this tab so people know it has something to see
							$('#species-badge').text(data.species.length);
							// Need this to force tab update
							$('#names-tabs li:eq(2) a').show();
						
							
							var html = '';
							html += '<div style="background-color:white;">';
							html += '<h4>Species names</h4>';
							html += '<p class="muted">Species names with "' + name + '" as genus</p>';
							html += '<table class="table table-striped table-hover table-bordered">';
							html += '<thead>';
							html += '<tr>';
							html += '<td>Year</td><td>Name</td>';
							html += '</tr>';
							html += '</thead>';
							html += '<tbody>';
							for (var i in data.species)
							{
								html += '<tr>';
								html += '<td>' + data.species[i].year + '</td>';
								html += '<td>' + '<a href="names/' + data.species[i].id + '">' + data.species[i].name + '</a>' + '</td>';
								html += '</tr>';
							}
							html += '</tbody>';
							html += '</table>';
							html += '</div>';
							$("#species").html(html);
						}
					}
				});
		}
		
		
		
	/* typeahead for search box */
	$("#q").typeahead({
	  source: function (query, process) {
		$.getJSON('http://bionames.org/bionames-api/name/' + query + '/suggestions?callback=?', 
		function (data) {
		  var suggestions = data.suggestions;
		  process(suggestions)
		})
	  }
	});
	
	show_concepts(id);
<?php
	$epithet = '';
	if (isset($doc->infraspecificEpithet)) {
		$epithet = $doc->infraspecificEpithet;
	} else if (isset($doc->specificEpithet)) {
		$epithet = $doc->specificEpithet;
	}

	if ($epithet != '') {
		if (isset($doc->taxonAuthor)) { 
		  $author =  $doc->taxonAuthor;
		  $author = preg_replace('/\(/', '', $author);
		  $author = preg_replace('/\)/', '', $author);
		  
		  $epithet = $epithet . ' ' . $author;
		  echo '	show_epithet("' . $epithet . '");' . "\n";
		}
	}
?>
	show_epithet(<?php echo '"' . $doc->nameComplete . '"'; ?>);
	show_related(<?php echo '"' . $doc->nameComplete . '"'; ?>);
	show_species(<?php echo '"' . $doc->nameComplete . '"'; ?>);
	show_publications(<?php echo '"' . $doc->nameComplete . '"'; ?>);
	
<?php
	if (isset($doc->publishedInCitation))
	{
		echo 'show_published_in(' . json_encode($doc->publishedInCitation) . ');';
	}
?>
	
</script>
</body>
</html>