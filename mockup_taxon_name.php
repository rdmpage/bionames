<?php

// mockup of taxon name display

$id = $_GET['id'];

?>
<!DOCTYPE html>
<html>
<head>
	<!--<base href="/bionames-api/" />-->
	<base href="/" />
	<title>Taxon Name</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>
	
	<script>	
	
		function add_metadata_stat(title,value) {
			$(display_stat(title,value)).appendTo($('#stats'));		
		}	
	
		function show_cluster(id)
		{
			$("#cluster").html("");
			
//			$.getJSON("http://bionames.org/bionames-api/id/" + id + "?callback=?",
			$.getJSON("api/id/" + id + "?callback=?",
				function(data){
					if (data.status == 200)
					{		
						// Display name
						var title = data.nameComplete;
						
						if (data.taxonAuthor)
						{
							title += ' ' + data.taxonAuthor;
						}
						
						$("#title").html(title);
						
						// Make browser window the name
						document.title = title;
						
						// Human readable
						
						var html = '';
						
						if (data.taxonAuthor)
						{	
							var author = data.taxonAuthor;
          					author = author.replace(/\(/, '');
          					author = author.replace(/\)/, '');  	
							
							html += '<div class="authority">';
							html += 'Published by ';
							//html += '<a href="mockup_search.php?q=' + encodeURIComponent(author) + '">'+author+'</a>.';
							html += '<a href="q/' + encodeURIComponent(author) + '">'+author+'</a>.';
							html += '</div>';
						}
						$("#cluster").html(html);
						
						
						// Publications
						var publications = [];
						for (var i in data.names)
						{
							if (data.names[i].publication)
							{
								var html = $("#cluster").html();
								html += '<div ';
								if (data.names[i].publishedInCitation) {
									html += ' id="publication' + data.names[i].publishedInCitation + '"';
								}								
								html += '>';
								html += data.names[i].publication;
								html += '</div>';
								$("#cluster").html(html);
							}
						}
						
						// Sidebar
						
						
						// Concepts
						show_concepts(id);
						
						// Variants and related names
						var epithet = '';
						if (data.infraspecificEpithet) {
							epithet = data.infraspecificEpithet;
						} else if (data.specificEpithet) {
							epithet = data.specificEpithet;
						}

						if (epithet != '') {
							if (data.taxonAuthor) { 
							  var author =  data.taxonAuthor;
							  author = author.replace(/\(/, '');
							  author = author.replace(/\)/, '');  
							  
							  epithet = epithet + ' ' + author;
							  show_epithet(epithet);
							}
						}
						
						show_related(data.nameComplete);
						
						
						// Publications with this name string
						show_publications(data.nameComplete);
						
						// Now flesh out publication details
						if (data.publishedInCitation) {
							show_name_publications(data.publishedInCitation);
						}
					}
				});				
			
		}
				
		function show_name_publications(publishedInCitation)
		{
			for (var id in publishedInCitation)
			{
				//$.getJSON("http://bionames.org/bionames-api/id/" + publishedInCitation[id] + "?callback=?",
				$.getJSON("api/id/" + publishedInCitation[id] + "?callback=?",
					function(data){
						if (data.status == 200) {
							//$('#publication' + publishedInCitation[id]).html(display_reference(data));
							
							show_snippet('publication' + publishedInCitation[id], data);
						}
					});
			}
				
		}		
		
		function show_concepts(id)
		{
			$("#concepts").html("");
			
//			$.getJSON("http://bionames.org/bionames-api/name/" + id + "/concepts?callback=?",
			$.getJSON("api/name/" + id + "/concepts?callback=?",
				function(data){
					if (data.status == 200)
					{	
						
						add_metadata_stat("Concepts", data.concepts.length);
						
						if (data.concepts.length > 0) {
						    var concepts = [];
							var html = '<h3>This name has been used for these taxa</h3>';
							for (var i in data.concepts) {
								html += '<div id="id' + data.concepts[i].concept.replace(/\//, '_') + '"></div>';
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
			
		function show_publications(name)
		{		
//			$.getJSON("http://bionames.org/bionames-api/name/" + encodeURIComponent(name) + "/publications?fields=title,thumbnail,identifier,author,journal,year&include_docs" + "&callback=?",
			$.getJSON("api/name/" + encodeURIComponent(name) + "/publications?fields=title,thumbnail,identifier,author,journal,year&include_docs" + "&callback=?",
				function(data){
					if (data.status == 200)
					{		
						
						add_metadata_stat("Publications", data.publications.length);
						
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

		
		function show_epithet(epithet)
		{
			$("#epithet").html("");
			
			//$.getJSON("http://bionames.org/bionames-api/name/" + encodeURIComponent(epithet) + "/epithet?callback=?",
			$.getJSON("api/name/" + encodeURIComponent(epithet) + "/epithet?callback=?",
				function(data){
					if (data.status == 200)
					{	
						if (data.names.length > 1) {					
							var html = '<h3>Names with same epithet</h3>';
							html += '<div style="text-align:right">';
							for (var i in data.names)
							{
								var s = data.names[i];
								//html += '<a href="mockup_search.php?q=' + encodeURIComponent(s) + '">' + s + '</a>' + '<br />';
								html += '<a href="q/' + encodeURIComponent(s) + '">' + s + '</a>' + '<br />';
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
			
//			$.getJSON("http://bionames.org/bionames-api/name/" + encodeURIComponent(name) + "/related?callback=?",
			$.getJSON("api/name/" + encodeURIComponent(name) + "/related?callback=?",
				function(data){
					if (data.status == 200)
					{	
						add_metadata_stat("Related Names", data.related.length);
						
						if (data.related.length > 0) {					
							var html = '<h3>Related names</h3>';
							html += '<ul>';
							for (var i in data.related)
							{
								var s = data.related[i];
//								html += '<li><a href="mockup_search.php?q=' + encodeURIComponent(s) + '">' + s + '</a></li>';
								html += '<li><a href="search/' + encodeURIComponent(s) + '">' + s + '</a></li>';
							}
							html += '</ul>';
							var current_html = $("#related").html();
							$("#related").html(current_html + html);
						}
					}
				});
		}
		
	</script>	
</head>
<body class="name">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>

	<div class="container-fluid">
	  <div class="row-fluid">
	    <div class="main-content span8">
			<ul class="nav nav-tabs">
			  <li class="bibliography active"><a href="#biblio-tab" data-toggle="tab">Bibliography</a></li>
			</ul>
			
			<div class="tab-content">
			  <div class="tab-pane active no-pad" id="biblio-tab">
		        <div id="publication-timeline" class="publication-timeline">
					<div class="pub-timeline">
						<div id="pubHistogram" class="chart"></div>
					</div>
		            <div id="pubList"></div>
		        </div>
			  </div>
		  </div>
	    </div>
	    <div class="sidebar span4">
			<div class="sidebar-header">
				<h1 id="title"></h1>
			</div>
			<div id="metadata" class="sidebar-metadata">
				<div id="stats" class="stats"></div>
				<div id="cluster"></div>
			</div>
	    	<div id="concepts" class="sidebar-section"></div>
	    	<div id="related" class="sidebar-section"></div>
	    	<div id="epithet" class="sidebar-section"></div>
	    </div>
	  </div>
	</div>


<script type="text/javascript">
		var id = "<?php echo $id;?>";
		show_cluster(id);
</script>

<script> if (use_livereload) { document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>'); }</script>

</body>
</html>