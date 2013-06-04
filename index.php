<?php 

// do PHP stuff here to get query parameters...

$q = '';
if (isset($_GET['q']))
{
	$q = $_GET['q'];
	header('Location: search/' . urlencode($q));
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
    <meta charset="utf-8">
    <title>BioNames</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
	<?php require 'stylesheets.inc.php'; ?>
	
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>


    <!-- Le styles -->
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 700px;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>
  </head>

  <body style="background-color:white;">
	<?php require 'analyticstracking.inc.php'; ?>
    <div class="container-narrow">

      <div class="masthead">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="dashboard">Dashboard</a></li>
          <!--
          <li><a href="about.html">About</a></li>
          <li><a href="#">Contact</a></li>
          -->
        </ul>
        <h3 class="muted">BioNames</h3>
      </div>

      <hr>

      <div class="jumbotron">
        <h1>BioNames</h1>
        <p class="lead">Taxa, text, and trees.</p>
<!--        <a class="btn btn-large btn-success" href="#">Sign up today</a> -->
 
			<form class="search" method="get" action="/">
				<input type="text" id='q' name='q' data-provide="typeahead" class="search-query large" placeholder="Search" autocomplete="off" value="<?php echo $q; ?>">
			</form> 
 
 		</div>
      
      
      
      
        <div class="row-fluid marketing" style="text-align:center">
        
        <a href="taxa/gbif/2432946"><img src="http://media.eol.org/content/2013/05/24/19/12833_88_88.jpg"/></a>
        <a href="issn/0022-8567"><img style="height:88px;" src="data:image/gif;base64,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" /></a>
        <a href="names/cluster/20705"><img  src="http://media.eol.org/content/2013/02/09/05/47072_88_88.jpg" /></a>
		<a href="references/ab2e0def5e24b38064ae74d4230b2e67"><img style="height:88px;" src="images/homepage-map" /></a>
		<a href="names/cluster/1715663"><img src="http://media.eol.org/content/2011/10/14/16/12619_88_88.jpg" /></a>
		<a href="http://bionames.org/authors/Oldfield%20Thomas"><img style="height:88px;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Portrait_of_Michael_Rogers_Oldfield_Thomas_-_ZooKeys-255-103-g003-bottom_right.jpeg/200px-Portrait_of_Michael_Rogers_Oldfield_Thomas_-_ZooKeys-255-103-g003-bottom_right.jpeg"></a>
    	<a href="taxa/ncbi/80974"><img src="http://media.eol.org/content/2008/10/08/12/27092_88_88.jpg" /></a>
   		<a href="taxa/gbif/1541332"><img src="http://media.eol.org/content/2011/10/14/16/38103_88_88.jpg" /></a>
    	</div>
    	
    	 <div class="row-fluid marketing" style="text-align:center">
			<!-- names -->
			<a href="http://www.organismnames.com/" rel="tooltip" title="Index of Organism Names (ION)" class="tip"><img style="height:48px;" src="images/logos/ion.png" /></a>
			<a href="http://data.gbif.org/" rel="tooltip" title="GBIF" class="tip"><img style="height:48px;" src="images/logos/GBIFwww_4.png" /></a>
			<a href="http://www.ncbi.nlm.nih.gov/" rel="tooltip" title="NCBI" class="tip"><img style="height:48px;" src="images/logos/ncbi-twitter.jpg" /></a>
			
			<!-- bibliography -->
			<a href="http://gallica.bnf.fr/" rel="tooltip" title="Gallica" class="tip"><img style="height:48px;" src="images/logos/gallica.jpg" /></a>
			<a href="http://ci.nii.ac.jp/" rel="tooltip" title="CiNii" class="tip"><img style="height:48px;" src="images/logos/twitter_bigger.png" /></a>
			<a href="http://www.worldcat.org/" rel="tooltip" title="WorldCat" class="tip"><img style="height:48px;" src="images/logos/twitter-worldcat.png" /></a>
			<a href="http://biostor.org/" rel="tooltip" title="BioStor" class="tip"><img style="height:48px;" src="images/logos/biostor-shadow.png" /></a>
			<a href="http://biodiversitylibrary.org/" rel="tooltip" title="Biodiversity Heritage Library" class="tip"><img style="height:48px;" src="images/logos/BHL_Small_Logo.jpg" /></a>
			<a href="http://www.crossref.org/" rel="tooltip" title="CrossRef" class="tip"><img style="height:48px;" src="images/logos/crossrefsquare.gif" /></a>
			<a href="http://retro.seals.ch/digbib/en/home" rel="tooltip" title="retro.seals.ch" class="tip"><img style="height:48px;" src="images/logos/logo_e-lib.ch.png" /></a>
			<a href="http://www.mendeley.com/" rel="tooltip" title="Mendeley" class="tip"><img style="height:48px;" src="images/logos/MendeleyIcon.png" /></a>
			
			<!-- software -->
			
			<a href="http://gallica.bnf.fr/" rel="tooltip" title="Gallica" class="tip">
			
			<a href="http://couchdb.apache.org/" rel="tooltip" title="CouchDB" class="tip"><img style="height:48px;" src="images/logos/couch.png" /></a>
			<a href="https://github.com/documentcloud/" rel="tooltip" title="DocumentCloud" class="tip"><img style="height:48px;" src="images/logos/documentcloud.png" /></a>
			<a href="https://bitbucket.org/fbennett/citeproc-js/wiki/Home/" rel="tooltip" title="Citeproc-js" class="tip"><img style="height:48px;" src="images/logos/citeproc-js-logo_avatar.png" /></a>
			<a href="http://twitter.github.io/bootstrap/" rel="tooltip" title="Boostrap" class="tip"><img style="height:48px;" src="images/logos/bootstrap-docs-readme.png" /></a>
			
			<!-- people -->
			
			<a href="https://trello.com/cynthiaparr" rel="tooltip" title="Adult supervision" class="tip"><img style="height:48px;" src="images/logos/170.png" /></a>
			<a href="https://github.com/rschenk/" rel="tooltip" title="Interface funkiness" class="tip"><img style="height:48px;" src="images/logos/ab4f2861e1f40c2092c09ba993a87462.jpeg" /></a>
			<a href="https://github.com/rdmpage/" rel="tooltip" title="Data wrangling" class="tip"><img style="height:48px;" src="images/logos/Mairi_drawing256x256.png" /></a>
			
			<!-- funding -->
			
			<a href="http://eol.org/" rel="tooltip" title="Funding" class="tip"><img style="height:48px;" src="images/logos/follow_eol_on_twitter.png" /></a>
    	
    	
    	</div>
      
      <div class="footer">
        <a href="http://iphylo.blogspot.com">Roderic D M Page</a><img src="https://secure.gravatar.com/avatar/b16ff4bc0fc20d123b346d86f531bda2?s=32" />
      </div>

    </div> <!-- /container -->
    
    <script>$('.tip').tooltip();</script>


  </body>
</html>