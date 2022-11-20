<?php

require_once(__DIR__ . '/config.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
	<base href="<?php echo $config['web_server'] . $config['web_root'] ?>" /><!--[if IE]></base><![endif]-->
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
	<script src="js/humanized_time_span.js"></script>
	
</head>
<body class="search">
	<?php require 'analyticstracking.inc.php'; ?>	
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid">
	
		<div class="row-fluid">
			<div class="span12">
		    	<div class="widget">
		    		<h4>Database</h4>
		    		<p class="muted">Tasks running in database</p>
			  		<div id="tasks"></div>
			  	</div>
		    </div>
		</div>

	
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
		    		<h4>Documents</h4>
		    		<p class="muted">Numbers of documents by category</p>
			  		<div id="documents"></div>
			  	</div>
		    </div>
		    <div class="span6">
		    	<div class="widget">
		    		<h4>Links</h4>
		    		<p class="muted">Numbers of URL and PDF links </p>
			  		<div id="links"></div>
			  	</div>
		    </div>
		</div>
		
		
		<div class="row-fluid">

			<!--
		    <div class="span6">
		    	<div class="widget">
		    		<h4>Journals</h4>
		    		<p class="muted">Journals in BioNames</p>
			  		<div id="issn"></div>
			  	</div>
		    </div>
		    -->
		
		</div>
		
	</div>

	<script type="text/javascript">
	
		function show_issn()
		{
			$("#issn").html('');
		
			$.getJSON("bionames-api/api_dashboard.php?issn&callback=?",
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
			
			$.getJSON("bionames-api/api_dashboard.php?identifiers&callback=?",
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
							html += data.rows[i].value.toLocaleString();
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
		
			$.getJSON("bionames-api/api_dashboard.php?publishers&callback=?",
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
		
		function show_links()
		{
			$("#links").html("");
			
			$.getJSON("bionames-api/api_dashboard.php?links&callback=?",
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
							html += data.rows[i].value.toLocaleString();
							html += '</td>';
							html += '</tr>';
						}
						html += '</table>';
						
						$("#links").html(html);
					}
				});
		}

		function show_documents()
		{
			$("#documents").html("");
			
			$.getJSON("bionames-api/api_dashboard.php?documents&callback=?",
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
							html += data.rows[i].value.toLocaleString();
							html += '</td>';
							html += '</tr>';
						}
						html += '</table>';
						
						$("#documents").html(html);
					}
				});
		}
		
		function show_tasks()
		{
			$("#tasks").html("");
			
			$.getJSON("bionames-api/api_status.php?callback=?",
				function(data){
					if (data.status == 200)
					{		
						var html = '';
						html += '<table class="table table-striped">';
						for (var i in data.tasks)
						{
							html += '<tr>';
							
							/*
							html += '<td>';
							
							html += data.tasks[i].type;
							
							html += '</td>';
							*/
							
							html += '<td>';
							if (data.tasks[i].design_document)
							{
								html += data.tasks[i].design_document;
							}
							html += '</td>';

							/*
							html += '<td>';
							if (data.tasks[i].changes_done)
							{
								html += data.tasks[i].changes_done + '/' + data.tasks[i].total_changes + ' ';
							}
							if (data.tasks[i].progress)
							{
								html += '(' + data.tasks[i].progress + '%)';
							}
							html += '</td>';
							*/
							
							
							html += '<td>';
							html += humanized_time_span(+new Date(data.tasks[i].started_on * 1000));
							html += '</td>';
							
							html += '</tr>';
							
							html += '<tr>';
							html += '<td colspan="2">';
							html += data.tasks[i].type;
							if (data.tasks[i].changes_done)
							{
								html += ' ' + data.tasks[i].changes_done + '/' + data.tasks[i].total_changes + ' ';
							}
							if (data.tasks[i].progress)
							{
								
								html += '(' + data.tasks[i].progress + '%)';
							}
							html += '</td>';							
							html += '</tr>';
							
							
							html += '<tr>';
							html += '<td colspan="2">';
							if (data.tasks[i].progress)
							{
								
								html += '<div class="progress progress-info">';
  								html += '<div class="bar" style="width: ' + data.tasks[i].progress + '%"></div>';
								html += '</div>';
								
							}
							html += '</td>';							
							html += '</tr>';
							
							
														
							
						}
						html += '</table>';
						
						$("#tasks").html(html);
					}
				});
		}
		
		
        show_tasks();
        show_identifiers();
		show_publishers();
		//show_issn();
		show_links();
		show_documents();
	</script>

	
	<!-- typeahead for search box -->
	<script>
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
	
	<script> if (use_livereload) { document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>'); }</script>

</body>
</html>