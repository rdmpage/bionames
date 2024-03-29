<?php

// mockup of journal display

require_once(__DIR__ . '/config.inc.php');

$issn = '';
$oclc = '';
$journal = '';

if (isset($_GET['issn']))
{
	$issn = $_GET['issn'];
}
if (isset($_GET['oclc']))
{
	$oclc = $_GET['oclc'];
}
if (isset($_GET['journal']))
{
	$journal = $_GET['journal'];
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
    
	<!--<script src="js/snippet.js"></script>   -->
	<script src="js/publication.js"></script>   
</head>
<body class="journal">
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>
	
	<div class="container-fluid">
	  <div class="row-fluid">
			<div class="span2">
<!--				<div id="volumes" class="affix"></div>  			-->
				<div id="volumes"></div>  			
			</div>
			
			<div class="span6">
				<div id="articles"></div>
			</div>
			
			<!--
			<div class="span2">
				<h4 id="title"></h4>
				<div id="metadata"></div>
				<div id="identifiers"></div>
			</div> -->
			
			<div class="sidebar span4">
				<div class="sidebar-header">
					<h1 id="title"></h1>
				</div>
				<div id="metadata" class="sidebar-metadata">
					<!-- <div id="stats" class="stats"></div> -->
					
					<div class="media">
						<div class="pull-right">
     						<img id="thumbnail" class="media-object" src="" style="width:100px;background-color:white;border:1px solid rgb(228,228,228)";>
     					</div>
     					<div id="journal_identifiers" class="media-body">     						
     					</div>
     				</div>
					<div id="rawcoverage"></div>
					<div id="history"></div>
					<div id="credit"></div>
					
					<div id="romeo"></div>
					
					<div id="map"></div>
					<div id="identifiers"></div>
					
				</div>
				
				<div>
					<?php require 'disqus.inc.php'; ?>
    			</div>
				
				
				
			</div>
			
			
		</div>
	</div>

<script type="text/javascript">
	var issn = "<?php echo $issn;?>";
	var oclc = "<?php echo $oclc;?>";
	var journal = "<?php echo $journal;?>";
	
	// Details about journal (OCLC)
	function display_journal_from_oclc (oclc)
	{
//		$.getJSON("http://bionames.org/bionames-api/journals/oclc/" + oclc + "?callback=?",
		$.getJSON("api/journals/oclc/" + oclc + "?callback=?",
			function(data){
				if (data.status == 200)
				{
					$('#title').html(data.title);
					
					document.title = data.title;
					
					/*
					$('#metadata').html('');
					
					var html = '';
					html += '<table>';
					html += '<tbody style="font-size:80%;">';
					html += '<tr>';
					html += '<td align="right" valign="top" style="color:rgb(128,128,128);">' + 'OCLC' + '</td>';
					html += '<td valign="top">' + oclc + '</td>';
					html += '</tr>';
					html += '</tbody>';
					html += '</table>';
					
					$('#metadata').html(html);
					*/
					
				}
			});
	}	

	// Details about journal (ISSN)
	function display_journal_from_issn (issn)
	{
//		$.getJSON("http://bionames.org/bionames-api/journals/issn/" + issn + "?callback=?",
		$.getJSON("api/journals/issn/" + issn + "?callback=?",
			function(data){
				if (data.status == 200)
				{
					$('#title').html(data.title);
				
					document.title = data.title;
					
					if (data.thumbnail) {
						$('#thumbnail').attr('src', data.thumbnail);					
					}

					// ISSNs and RSS feed
					html = '';
					html += '<table class="table">';
					html += '<tbody>';
					
					for (var i in data) {
						switch (i)
						{
							case 'issnl':
								html += '<tr>';
								html += '<td class="muted">ISSN-L</td>';
//								html += '<td>' + '<a href="mockup_journal.php?issn=' + data[i] + '">' + data[i] + '</a>' + '</td>';
								html += '<td>' + '<a href="issn/' + data[i] + '" rel="tooltip" title="The linking ISSN (ISSN-L) ' + data[i] + ' groups together different ISSNs for the same resource" class="tip">' + data[i] + '</a>' + '</td>';
								html += '</tr>';
								break;
						
							case 'issn':
							//case 'publisher':
							//case 'rawcoverage':
								html += '<tr>';
								html += '<td class="muted">' + i.toUpperCase() + '</td>';
								html += '<td>' + '<a href="issn/' + data[i] + '" rel="tooltip" title="The International Standard Serial Number (ISSN) ' + data[i] + ' is a unique identifier for this journal" class="tip">' + data[i] + '</a>' + '</td>';
								html += '</tr>';
								break;
								
							case 'rssurl':
								html += '<tr>';
								html += '<td class="muted">Latest articles</td>';
								html += '<td>' + '<a href="' + data[i] + '" target="_new">' + 'RSS' + '</a>' + '</td>';
								html += '</tr>';
								break;
								
							case 'wikidata':	
								html += '<tr>';
								html += '<td class="muted">Wikidata</td>';
								html += '<td>' + '<a href="https://www.wikidata.org/wiki/' + data[i] + '" target="_new" rel="tooltip" title="Wikidata item for this journal" class="tip"><i class="icon-share"></i>' + data[i] + '</a>' + '</td>';
								html += '</tr>';
								break;								
								
								
							case 'wikipedia':	
								html += '<tr>';
								html += '<td class="muted">Wikipedia</td>';
								html += '<td>' + '<a href="https://en.wikipedia.org/wiki/' + data[i] + '" target="_new" rel="tooltip" title="Wikipedia page for this journal" class="tip"><i class="icon-share"></i>' + data[i] + '</a>' + '</td>';
								html += '</tr>';
								break;
				
																
							default:
								break;
						}
					}
					html += '</tbody>';
					html += '</table>';
					$('#journal_identifiers').html(html);
					
					$('.tip').tooltip();
					
					// Text details
					if (data.rawcoverage) {
						$('#rawcoverage').html(data.rawcoverage);
					}
					
					
					// History
					html = '';
					if (data.preceding)
					{
						if (data.preceding.length > 0 )
						{
							html += '<b>Preceding</b>';
							html += '<ul>';
							for (var i in data.preceding)
							{
//								html += '<li>' + '<a href="mockup_journal.php?issn=' + data.preceding[i] + '">' + data.preceding[i] + '</a>' + '</li>';
								html += '<li>' + '<a href="issn/' + data.preceding[i] + '" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'container\', \'container\', 0]);">' + data.preceding[i] + '</a>' + '</li>';
							}
							html += '</ul>';
						}
					}
					if (data.succeeding)
					{
						if (data.succeeding.length > 0 )
						{
							html += '<b>Succeeding</b>';
							html += '<ul>';
							for (var i in data.succeeding)
							{
								//html += '<li>' + '<a href="mockup_journal.php?issn=' + data.succeeding[i] + '">' + data.succeeding[i] + '</a>' + '</li>';
								html += '<li>' + '<a href="issn/' + data.succeeding[i] + '" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'container\', \'container\', 0]);">' + data.succeeding[i] + '</a>' + '</li>';
							}
							html += '</ul>';
						}
					}
					
					/*
					if (data.other)
					{
						if (data.other.length > 0 )
						{
							html += '<b>Other</b>';
							html += '<ul>';
							for (var i in data.other)
							{
//								html += '<li>' + '<a href="mockup_journal.php?issn=' + data.other[i] + '">' + data.other[i] + '</a>' + '</li>';
								html += '<li>' + '<a href="issn/' + data.other[i] + '">' + data.other[i] + '</a>' + '</li>';
							}
							html += '</ul>';
						}
					}
					*/
					$('#history').html(html);
					
					// Credit
					html = '<small>Data from <a href="http://www.worldcat.org/issn/' + issn + '" target="_new">WorldCat</a></small>';
					$('#credit').html(html);
					
					
					
				}
			});
	}
	
	
		// Map of localities (not working)
		function show_journal_points(ns, issn)
		{
			$.getJSON("api/journals/issn/" + issn + "/geometry?callback=?",
				function(data){
					if (data.status == 200)
					{
						//var html = 'map here';
						
						//$('#map').html(html);
						
						$.post('map.php', JSON.stringify(data.coordinates), function(data) {
  							$('#map').html(data);
						});
					}
				});
		}
	
	
		function year_volume_articles(ns, value, volume, year)
		{
			$("#articles").html("");
			
			var url = '';
			switch (ns)
			{
				case 'oclc':
					//url = 'http://bionames.org/bionames-api/journals/oclc/' + value + '/volumes/' + volume + '/year/' + year;
					url = 'api/journals/oclc/' + value + '/volumes/' + volume + '/year/' + year;
					break;
					
				case 'issn':
				default:
					//url = 'http://bionames.org/bionames-api/journals/issn/' + value + '/volumes/' + volume + '/year/' + year;
					url = 'api/journals/issn/' + value + '/volumes/' + volume + '/year/' + year;
					break;
			}
			url += '?callback=?';
			
			
			$.getJSON(url,
				function(data){
					if (data.status == 200)
					{					
						var html = '';
						var ids = [];
						for (var id in data.articles)
						{
							var html_id = data.articles[id];
							html_id = html_id.replace(/\//g, '_');
							html_id = html_id.replace(/-/g, '_');
							html_id = html_id.replace(/\./g, '_');
							html_id = html_id.replace(/\(/g, '_');
							html_id = html_id.replace(/\)/g, '_');
							html_id = html_id.replace(/\[/g, '_');
							html_id = html_id.replace(/\]/g, '_');
							html_id = html_id.replace(/:/g, '_');
							html_id = html_id.replace(/;/g, '_');

							html += '<div id="id' + html_id + '">' + data.articles[id] + '</div>';
							ids.push(data.articles[id]);
						}
						// display details
						for (var id in ids) {
							html += '<script>display_publications("' + ids[id] + '", "container");<\/script>';
						}

						$("#articles").html(html);
						
						// http://stackoverflow.com/a/1145297/9684
						$("html, body").animate({ scrollTop: 0 }, "slow");
					}
				});
		}
						
	
	
			 function show_journal_volumes(ns, value)
			  {
				$("#volumes").html("");
				
				var url = '';
				switch (ns)
				{
					case 'oclc':
//						url = 'http://bionames.org/bionames-api/journals/oclc/' + value + '/volumes';
						url = 'api/journals/oclc/' + value + '/volumes';
						break;
						
					case 'issn':
					default:
//						url = 'http://bionames.org/bionames-api/journals/issn/' + value + '/volumes';
						url = 'api/journals/issn/' + value + '/volumes';
						break;
				}
				url += '?callback=?';
				
				$.getJSON(url,
					function(data){
						var html = '';
						if (data.status == 200)
						{							
							html += '<div class="accordion" id="accordion" >';
							
							if (data.decades)
							{
								var first = true;
								for (var decade in data.decades)
								{								
									html += '<div class="accordion-group" style="background-color:white;">';
									html += '  <div class="accordion-heading">';
									html += '  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse' + decade + '">';
									html += decade + '\'s';
									html += '</a>';
									html += '  </div>';
									
									if (first) {
										html += '  <div id="collapse' + decade + '" class="accordion-body collapse in">';
										first = false;
									} else {
										html += '  <div id="collapse' + decade + '" class="accordion-body collapse">';
									}
									html += '    <div class="accordion-inner" id="accordionInner' + decade + '">';

									html += '<ul>';
									for (var year in data.decades[decade])
									{
										html += '<li>' + year ;
										html += '<ul>'
										for (volume in data.decades[decade][year])
										{
											html += '<li>';
											html += '<span style="cursor: pointer" onclick="year_volume_articles(\'' + ns + '\', \'' + value + '\',\'' + data.decades[decade][year][volume].volume + '\',' + year + ')">';
											html += ' vol. ' + data.decades[decade][year][volume].volume + '</span>';
											html += ' <span class="badge badge-info">' +  data.decades[decade][year][volume].count + '</span>';
											html += '</li>';	
										}
										html += '</ul>';
										html += '</li>';
									}
									html += '</ul>';
									html +=  '    </div>';
									html +=  '  </div>';
									html +=  '</div>';
								}
							}
							
							html += '</div>';
						}
						else
						{
							html += 'Badness';
						}
						$("#volumes").html(html);
					});
				}
				
		function show_article_identifiers(ns, value)
		{
			$("#identifiers").html("");
			
			var url = '';
			switch (ns)
			{
				case 'oclc':
//					url = 'http://bionames.org/bionames-api/journals/oclc/' + value + '/articles/identifiers';
					url = 'api/journals/oclc/' + value + '/articles/identifiers';
					break;
					
				case 'issn':
				default:
//					url = 'http://bionames.org/bionames-api/journals/issn/' + value + '/articles/identifiers';
					url = 'api/journals/issn/' + value + '/articles/identifiers';
					break;
			}
			url += '?callback=?';
			
			$.getJSON(url,
				function(data){
					var html = '';
					if (data.status == 200)
					{			
						html += '<div style="position:relative;">';

						html += '<h5>Identifier coverage</h5>';
						html += '<small>DOI, Handle, BioStor, JSTOR, CiNii, PMID, PMC, ZooBank</small>';
						html += '<div style="position:relative;">';
						for (var i in data.years)
						{
							for (j in data.years[i])
							{
								var ids=[];
								var opacity = 0.1;
								for (k in data.years[i][j])
								{
									if (data.years[i][j][k].indexOf('biostor') != -1)
									{
										ids.push('BioStor');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('cinii') != -1)
									{
										ids.push('CiNii');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('doi') != -1)
									{
										ids.push('DOI');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('handle') != -1)
									{
										ids.push('Handle');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('jstor') != -1)
									{
										ids.push('JSTOR');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('pmc') != -1)
									{
										ids.push('PMC');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('pmid') != -1)
									{
										ids.push('PMID');
										opacity += 0.2;
									}		
									if (data.years[i][j][k].indexOf('wikidata') != -1)
									{
										ids.push('WIKIDATA');
										opacity += 0.2;
									}																									
									if (data.years[i][j][k].indexOf('zoobank') != -1)
									{
										ids.push('ZOOBANK');
										opacity += 0.2;
									}									
								}
								ids = ids.sort();
								html += '<div style="float:left;width:14px;height:14px;">';
//								html += '<a href="mockup_publication.php?id=' + j + '" title="' + ids.join() + '" rel="tooltip" class="tip">';
								html += '<a href="references/' + j + '" title="' + ids.join() + '" rel="tooltip" class="tip" onClick="_gaq.push([\'_trackEvent\', \'Internal\', \'container\', \'work\', 0]);">';
								html += '<div style="width:12px;height:12px;background-color:green;margin:1px;opacity:' + opacity + '"></div>';
								html += '</a>';
								html += '</div>';
							}
						}
						
						html += '<div style="clear:both;"></div>';
						html += '</div>';

						html += '<h5>Links</h5>';
						html += '<small>URL or PDF link</small>';
						html += '<div style="position:relative;">';
						for (var i in data.years)
						{
							for (j in data.years[i])
							{
								var ids=[];
								var opacity = 0.1;
								for (k in data.years[i][j])
								{
									if (data.years[i][j][k].indexOf('LINK') != -1)
									{
										ids.push('URL');
										opacity += 0.2;
									}
									if (data.years[i][j][k].indexOf('PDF') != -1)
									{
										ids.push('PDF');
										opacity += 0.2;
									}
								}
								ids = ids.sort();
								html += '<div style="float:left;width:14px;height:14px;">';
//								html += '<a href="mockup_publication.php?id=' + j + '" title="' + ids.join() + '" rel="tooltip" class="tip">';
								html += '<a href="references/' + j + '" title="' + ids.join() + '" rel="tooltip" class="tip">';
								html += '<div style="width:12px;height:12px;background-color:green;margin:1px;opacity:' + opacity + '"></div>';
								html += '</a>';
								html += '</div>';
							}
						}
						html += '<div style="clear:both;"></div>';
						html += '</div>';


						
					}
					$("#identifiers").html(html);
					$('.tip').tooltip();
				});
		}
	
					
					
			 function show_romeo(issn)
			  {
				$("#romeo").html("");
				
				var url = 'bionames-api/journals/issn/' + issn + '/romeo?callback=?';
				
				$.getJSON(url,
					function(data){
						var html = '';
						if (data.status == 200)
						{						
							html += '<h5>Publisher copyright and archiving</h5>';
							if (data.romeocolour) {
							   switch (data.romeocolour) {
							   		case 'green':
							   			html += '<div style="background-color:#D0F9D2;">This is a ROMEO green journal</div>';
							   			break;
							   		case 'blue':
							   			html += '<div style="background-color:#D3ECFA;">This is a ROMEO blue journal</div>';
							   			break;
							   		case 'yellow':
							   			html += '<div style="background-color:#FFFF99;">This is a ROMEO yellow journal</div>';
							   			break;
							   		case 'white':
							   			html += '<div style="background-color:#FFFFFF;">This is a ROMEO white journal</div>';
							   			break;
							   	}
							} else {
								html += '<div>Not in ROMEO</div>';
							}
							html += '<ul class="unstyled">';
							
							
							if (data.paidaccessnotes) {
								html += '<li><i class="icon-info-sign"></i>' + data.paidaccessnotes + '</li>';
							}
							
							
							if (data.prearchiving) {
								switch (data.prearchiving) {
									case 'can':
										html += '<li><i class="icon-ok"></i>Author can archive pre-print</li>';
										break;
									case 'cannot':
										html += '<li><i class="icon-ban-circle"></i>Author cannot archive pre-print</li>';
										break;
									case 'restricted':
										html += '<li><i class="icon-warning-sign"></i>Author can archive pre-print (restricted)</li>';
										break;
									default:
										break;
								}
							}
							if (data.postarchiving) {
								switch (data.postarchiving) {
									case 'can':
										html += '<li><i class="icon-ok"></i>Author can archive post-print</li>';
										break;
									case 'cannot':
										html += '<li><i class="icon-ban-circle"></i>Author cannot archive post-print</li>';
										break;
									case 'restricted':
										html += '<li><i class="icon-warning-sign"></i>Author can archive post-print (restricted)</li>';
										break;
									default:
										break;
								}
							}
							if (data.pdfarchiving) {
								switch (data.pdfarchiving) {
									case 'can':
										html += '<li><i class="icon-ok"></i>Author can archive PDF</li>';
										break;
									case 'cannot':
										html += '<li><i class="icon-ban-circle"></i>Author cannot archive PDF</li>';
										break;
									case 'restricted':
										html += '<li><i class="icon-warning-sign"></i>Author can archive PDF (restricted)</li>';
										break;
									default:
										break;
								}
							}
							html += '</ul>';
							
							html += '<div><small>Data from <a href="http://www.sherpa.ac.uk/romeo/issn/' + issn + '/" target="_new">SHERPA/ROMEO</a></small></div>';
						}
						else
						{
							html += 'Badness';
						}
						$("#romeo").html(html);
					});
				}
					
					
	if (issn != '')
	{
		display_journal_from_issn(issn);
		show_romeo(issn);
		show_journal_volumes('issn', issn);
		show_article_identifiers('issn', issn);
		//show_journal_points('issn', issn);
	}
	
	if (oclc != '')
	{
		display_journal_from_oclc(oclc);
		show_journal_volumes('oclc', oclc);
		show_article_identifiers('oclc', oclc);
	}
	
	if (journal != '')
	{		
		$('#title').html(journal);
	}

	<!-- typeahead for search box -->
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



</body>
</html>