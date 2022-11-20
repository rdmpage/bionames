<?php

require_once(__DIR__ . '/config.inc.php');

?>
<!DOCTYPE html>
<html>
<head>
	<base href="<?php echo $config['web_server'] . $config['web_root'] ?>" /><!--[if IE]></base><![endif]-->
	<title>API documentation</title>
	
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
	
</head>
<body class="search">
	<?php require 'analyticstracking.inc.php'; ?>	
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid">

		<h1>API Documentation</h1>
		<p class="muted">This is preliminary and incomplete documentation. Unless otherwise stated, all methods return JSON, and support the "callback" parameter.</p>

		<div class="row-fluid">
			<div class="span12">
				<div class="widget">
					<h3>Object</h3>
					<p class="muted">Return information on an object.</p>

					<h4>api/id/&lt;id&gt;</h4>
					<p class="muted">Return object in JSON</p>
					<a href="api/id/d0459eea15b7045816902849b540421d" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/id/d0459eea15b7045816902849b540421d</a>

					<h4>api/id/&lt;id&gt;/thumbnail</h4>
					<p class="muted">Return URL of thumbnail for image of object</p>
					<a href="api/id/d0459eea15b7045816902849b540421d/thumbnail" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/id/d0459eea15b7045816902849b540421d/thumbnail</a>

					<h4>api/id/&lt;id&gt;/thumbnail/image</h4>
					<p class="muted">Return thumbnail image of object</p>
					<a href="api/id/d0459eea15b7045816902849b540421d/thumbnail/image" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/id/d0459eea15b7045816902849b540421d/thumbnail/image</a>

				</div>
			</div>
		</div>
		
		<p />
		
		<div class="row-fluid">
			<div class="span12">
				<div class="widget">
					<h3>Authors</h3>
					<p class="muted">Return information about an author.</p>

					<h4>api/authors/&lt;name&gt;/coauthors</h4>
					<p class="muted">Return names of coauthors</p>
					<a href="api/authors/Darrel%20R%20Frost/coauthors" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/authors/Darrel R Frost/coauthors</a>

					<h4>api/authors/&lt;name&gt;/publications</h4>
					<p class="muted">Publications by author</p>
					<a href="api/authors/Darrel%20R%20Frost/publications" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/authors/Darrel R Frost/publications</a>

					<h4>api/authors/&lt;name&gt;/publications/years</h4>
					<p class="muted">Taxa published in papers with this author</p>
					<a href="api/authors/Darrel%20R%20Frost/publications/taxa" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/authors/Darrel R Frost/publications/taxa</a>

				</div>
			</div>
		</div>
		
		<p />

		<div class="row-fluid">
			<div class="span12">
				<div class="widget">
					<h3>References</h3>
					<p class="muted">Methods specific to a references (articles, books, etc.)</p>

					<h4>api/publication/&lt;id&gt;/citedby</h4>
					<p class="muted">Return references that cite this reference</p>
					<a href="api/publication/d0459eea15b7045816902849b540421d/citedby" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/publication/d0459eea15b7045816902849b540421d/citedby</a>

					<h4>api/publication/&lt;id&gt;/names</h4>
					<p class="muted">Taxonomic names published by this reference</p>
					<a href="api/publication/d0459eea15b7045816902849b540421d/names" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/publication/d0459eea15b7045816902849b540421d/names</a>
					
					<h4>api/publication/&lt;id&gt;/text</h4>
					<p class="muted">Full text of this publication (if available)</p>
					<a href="api/publication/d3c7419c9b9dcd9d545d49fd1b2b13eb/text" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/publication/d3c7419c9b9dcd9d545d49fd1b2b13eb/text</a>
					
				</div>
			</div>
		</div>
		
		<p />

		<div class="row-fluid">
			<div class="span12">
				<div class="widget">
					<h3>Search</h3>
					<p class="muted">Search on a taxon name. Returns multiple types of object.</p>

					<h4>api/search/&lt;id&gt;</h4>
					<p class="muted">Return objects that match the search term</p>
					<a href="api/search/Pristimantis" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/search/Pristimantis</a>
					
				</div>
			</div>
		</div>
		
		<p />
		
		<div class="row-fluid">
			<div class="span12">
				<div class="widget">
					<h3>Taxon name</h3>
					<p class="muted">Methods specific to taxon names</p>

					<h4>api/name/&lt;namestring&gt;/didyoumean</h4>
					<p class="muted">Name strings that are similar to query string</p>
					<a href="api/name/Simulium%20selwynense/didyoumean" target="_new"><?php echo $config['web_server'] . $config['web_root'] ?>api/name/Simulium%20selwynense/didyoumean</a>
					
				</div>
			</div>
		</div>
		

		<p />
		
	</div>


	
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