<?php


?>
<!DOCTYPE html>
<html>
<head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
	<title>Dashboard</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	
<style type="text/css" title="text/css">
	.widget {
		padding: 20px;
		background-color: #fff;
		border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);	
    }
		
</style>

	
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>

	<script src="vendor/d3js/d3.v3.min.js"></script>
	
</head>
<body class="search">
	<?php require 'analyticstracking.inc.php'; ?>	
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid">
		<div class="row-fluid">
		    <div class="span6">
		    	<div class="widget">
		    		<h4>Publishers</h4>
		    		<p class="muted">Sources of publications in BioNames</p>
			  		<div id="publishers"></div>
			  	</div>
		    </div>
		    
		    
		    <div class="span6">
		    	<div class="widget">
		    		<h4>Identifiers</h4>
		    		<p class="muted">Total number of identifiers by category</p>
			  		<div id="identifiers"></div>
			  	</div>
		    </div>
		</div> 
		<div class="row-fluid">
		    <div class="span6">
		    	<div class="widget">
		    		<h4>Journals</h4>
		    		<p class="muted">Journals in BioNames</p>
			  		<div id="issn"></div>
			  	</div>
		    </div>
		</div>
	</div>

	<script type="text/javascript">
	
		function show_issn()
		{
			$("#issn").html('');
		
			$.getJSON("http://bionames.org/bionames-api/api_dashboard.php?issn&callback=?",
				function(data){
					if (data.status == 200)
					{
						var r = jQuery("#issn").width(),
							format = d3.format(",d"),
							fill = d3.scale.category20c();
						var bubble = d3.layout.pack()
							.sort(null)
							.size([r, r]);
						var vis = d3.select("#issn").append("svg:svg")
							.attr("width", r)
							.attr("height", r)
							.attr("class", "bubble")
						var node = vis.selectAll("g.node")
							.data(bubble(data)
							.filter(function(d) { return !d.children; }))
							.enter().append("svg:g")
							.attr("class", "node")
							.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
						node.append("svg:title")
							.text(function(d) { return d.data.name + " : " + format(d.data.value) + " articles"})
						node.append("svg:circle")
							.attr("r", function(d) { return d.r; })
							.style("fill", function(d) { return fill(d.data.name); })
						node.append("svg:text")
							.attr("text-anchor", "middle")
							.attr("dy", ".3em")
							.attr("style", "font-size:10px")
							.text(function(d) { if (d.data.name) { return d.data.name.substr(0, d.r/3);} else { return ""; } })
						node.on("click",function(d) {
							clickbubble(d.data.issn)});
					
					}
				});
		}
		
        var clickbubble = function(issn) {
            window.location = "issn/" + issn;
        };
		
	
	
		function show_identifiers()
		{
			$("#identifiers").html("");
			
			$.getJSON("http://bionames.org/bionames-api/api_dashboard.php?identifiers&callback=?",
				function(data){
					if (data.status == 200)
					{		
						var html = '';
						html += '<table class="table table-striped">';
						for (var i in data.rows)
						{
							html += '<tr>';
							html += '<td>';
							
							switch (data.rows[i].key)
							{
								default:
									html += data.rows[i].key.toUpperCase();
									break;
							}
							
							//html += data.rows[i].key;
							html += '</td>';
							html += '<td style="text-align:right">';
							html += data.rows[i].value;
							html += '</td>';
							html += '</tr>';
						}
						html += '</table>';
						
						$("#identifiers").html(html);
					}
				});
		}
	

		function show_publishers()
		{
			$("#publishers").html("");
		
			$.getJSON("http://bionames.org/bionames-api/api_dashboard.php?publishers&callback=?",
				function(data){
					if (data.status == 200)
					{
						var r = jQuery("#publishers").width(),
							format = d3.format(",d"),
							fill = d3.scale.category20c();
						var bubble = d3.layout.pack()
							.sort(null)
							.size([r, r]);
						var vis = d3.select("#publishers").append("svg:svg")
							.attr("width", r)
							.attr("height", r)
							.attr("class", "bubble")
						var node = vis.selectAll("g.node")
							.data(bubble(data)
							.filter(function(d) { return !d.children; }))
							.enter().append("svg:g")
							.attr("class", "node")
							.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
						node.append("svg:title")
							.text(function(d) { return d.data.name + " : " + format(d.data.value) + " articles"})
						node.append("svg:circle")
							.attr("r", function(d) { return d.r; })
							.style("fill", function(d) { return fill(d.data.name); })
						node.append("svg:text")
							.attr("text-anchor", "middle")
							.attr("dy", ".3em")
							.attr("style", "font-size:10px")
							.text(function(d) { if (d.data.name) { return d.data.name.substr(0, d.r/3);} else { return ""; } }
							);
					
					}
				});
		}
        
        show_identifiers();
		show_publishers();
		show_issn();
	</script>

	
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