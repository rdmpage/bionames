/* Display article */
function display_publications(id) {
//	$.getJSON("http://bionames.org/bionames-api/id/" + id + "?callback=?",
	$.getJSON("api/id/" + id + "?callback=?",
		function(data){
			if (data.status == 200)
			{		
				
				var element_id = 'id' + id;
				element_id = element_id.replace(/\//g, '_');
				element_id = element_id.replace(/\-/g, '_');
				element_id = element_id.replace(/\./g, '_');
				element_id = element_id.replace(/\]/g, '_');
				element_id = element_id.replace(/\[/g, '_');
				element_id = element_id.replace(/\)/g, '_');
				element_id = element_id.replace(/\(/g, '_');
				element_id = element_id.replace(/;/g, '_');
				element_id = element_id.replace(/:/g, '_');
				element_id = element_id.replace(/</g, '_');
				element_id = element_id.replace(/>/g, '_');
				
				console.log ('element_id=' + element_id);
				
				$('#' + element_id).html('hello');
				
				show_publication(element_id, data);
			}
		});
}

/* Display article (possibly multiple elements with same id) */
function display_publications_matching(id) {
	$.getJSON("api/id/" + id + "?callback=?",
		function(data){
			if (data.status == 200) 
			{
				$('div').each(function() {
					if ($(this).attr('id')) {
						
						var pattern = '/' + id + '/';
						if( $(this).attr('id').match(/id/) ) 
					 	{
					 	  console.log($(this).attr('id'));
						  show_publication($(this).attr('id'), data);
					 	}						
						
					}
				});
			}
		});
}

/* Display publication thumbnail where id is also id of DOM element (with 'id' prefix) */
function display_publication_thumbnails(id) {
//	$.getJSON("http://bionames.org/bionames-api/id/" + id + "?callback=?",
	$.getJSON("api/id/" + id + "?callback=?",
		function(data){
			if (data.status == 200)
			{		
				var element_id = 'id' + id;
				element_id = element_id.replace(/\//g, '_');
				element_id = element_id.replace(/\./g, '_');
				element_id = element_id.replace(/\-/g, '_');
				element_id = element_id.replace(/\]/g, '_');
				element_id = element_id.replace(/\[/g, '_');
				element_id = element_id.replace(/\)/g, '_');
				element_id = element_id.replace(/\(/g, '_');
				element_id = element_id.replace(/;/g, '_');
				element_id = element_id.replace(/:/g, '_');
				element_id = element_id.replace(/</g, '_');
				element_id = element_id.replace(/>/g, '_');
				
				show_publication_thumbnail(element_id, data);
			}
		});
}

/* Display publication thumbnail where id is also name of DOM element that has publication id 
as value of attribute data-id */
function display_publication_thumbnails_data_id(id) {
	
	var publication_id = $('#' + id).attr('data-id');
	
	if (publication_id) {	
		$.getJSON("api/id/" + publication_id + "?callback=?",
//		$.getJSON("http://bionames.org/bionames-api/id/" + publication_id + "?callback=?",
			function(data){
				if (data.status == 200)
				{		
					show_publication_thumbnail(id, data);
				}
			});
	}
}
	

/* Display one article */
function show_publication (element_id, doc) {
	var html = '';
	
	html += '<div class="media" style="border-top:1px solid #e5e5e5;margin-bottom:10px;padding-top:10px;">';
	
	switch (doc.type) {
		case 'generic':
			// Unparsed publication?
			html += '<div class="media-body" style="background-color:orange;">';			
			html += '<a href="references/' + doc._id + '">';
			html += doc.citation_string;
			html += '</a>';
			html += '</div><!-- class="media-body" -->';
			break;
		
		case 'article':
		case 'book':
		case 'chapter':
		case 'thesis':
			// Publication snippet
//			html += '<a class="pull-right" href="mockup_publication.php?id=' + doc._id + '">';
			html += '<a class="pull-right" href="references/' + doc._id + '">';
			if (doc.thumbnail) {
				html += '<img class="media-object" style="border:1px solid #e5e5e5;background-color:white;" width="64" src="' + doc.thumbnail + '"/>';
			} else {
				html += '<div class="thumbnail_blank">';
				doi = has_doi(doc);
				if (doi != '')
				{
					html += '<img src="images/doi16x16.png" style="float:right;"/>';
				}				
				html += '</div>';
			}
			html += '</a>';
						
			html += '<div class="media-body">';
			if (doc.title)
			{
//				html += '<a href="mockup_publication.php?id=' + doc._id + '">';
				html += '<a href="references/' + doc._id + '">';
				html += '<h5>' + doc.title + '</h5>';
				html += '</a>';
			}
			
			html += '<!-- begin metadata -->';			
			html += '<div>';
			if (doc.author)
			{
				html += 'by ';
				for (var j in doc.author)
				{
					//html += '<a href="mockup_author.php?name=' + doc.author[j].name + '">'
					html += doc.author[j].name;
					//html += '</a>';
					html += '; ';
				}
			}
			html += '</div>';
			
			html += '<div>';
			if (doc.journal)
			{
				if (doc.journal.name)
				{
					html += '<span class="journal">' + doc.journal.name + '</span>';
				}
				if (doc.journal.volume)
				{
					html += ' ' + '<span class="volume">' + doc.journal.volume + '</span>';
				}
				if (doc.journal.issue)
				{
					html += '(' + doc.journal.issue + ')';
				}
				if (doc.journal.pages)
				{
					html += ' pages ' + doc.journal.pages;
				}
			}
			
			if (doc.book)
			{
				if (doc.type == 'chapter') {					
					if (doc.book.title)
					{
						html += '<em>in</em> ' + doc.book.title + '</span>';
					}
					if (doc.book.pages)
					{
						html += ' pages ' + doc.book.pages;
					}
				}
			}
			
			
			if (doc.year)
			{
				html += ' (' + doc.year + ')';
			}
			html += '</div>';
			
			if (doc.identifier)
			{
				html += '<ul class="identifier" style="color:rgb(128,128,128);">';
				for (var j in doc.identifier)
				{
					switch (doc.identifier[j].type)
					{
						case "ark":
							html += '<li>';
							//html += '<a href="http://biostor.org/reference/' + doc.identifier[j].id + '" target="_new">';
							//html += 'ark:/' + doc.identifier[j].id;
							html += 'ARK ark:/' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
					
						case "biostor":
							html += '<li>';
							//html += '<a href="http://biostor.org/reference/' + doc.identifier[j].id + '" target="_new">';
							html += 'BioStor ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
		
						case "cinii":
							html += '<li>';
							//html += '<a href="http://ci.nii.ac.jp/naid/' + doc.identifier[j].id + '" target="_new">';
							html += 'CINII ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
							
						case "doi":
							html += '<li>';
							//html += '<a href="http://dx.doi.org/' + doc.identifier[j].id + '" target="_new">';
							html += 'DOI ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
		
						case "handle":
							html += '<li>';
							//html += '<a href="http://hdl.handle.net/' + doc.identifier[j].id + '" target="_new">';
							html += 'HDL ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
		
						case "jstor":
							html += '<li>';
							//html += '<a href="http://www.jstor.org/stable' + doc.identifier[j].id + '" target="_new">';
							html += 'JSTOR ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
							
						case "pmc":
							html += '<li>';
							//html += '<a href="http://www.jstor.org/stable' + doc.identifier[j].id + '" target="_new">';
							html += 'PMC ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
							
						case "pmid":
							html += '<li>';
							//html += '<a href="http://www.jstor.org/stable' + doc.identifier[j].id + '" target="_new">';
							html += 'PMID ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;

						case "zoobank":
							html += '<li>';
							//html += '<a href="http://zoobank.org/stable' + doc.identifier[j].id + '" target="_new">';
							html += 'ZOOBANK ' + doc.identifier[j].id;
							//html += '</a>';
							html += '</li>';
							break;
							
							
						default:
							break;
					}
				}	
				html += '</ul>';
			}
			
			if (doc.book) {
				if (doc.book.identifier) {
					html += '<ul class="identifier">';
					for (var j in doc.book.identifier) {
						switch (doc.book.identifier[j].type)
						{
							case "isbn":
								html += '<li>';
								html += 'ISBN ' + doc.book.identifier[j].id;
								html += '</li>';
								break;
							
							default:
								break;
						}
					}
					html += '</ul>';
				}
			}
		
			
			html += '<!-- end metadata -->';
			html += '</div><!-- class="media-body" -->';
			break;
			
			
		default:
			break;
	}
	html += '</div>'; <!-- class="media" -->
	
	//console.log(html);
	//console.log(element_id);
	
	$('#' + element_id).html(html);
}


/* Display one article */
function show_publication_thumbnail (element_id, doc) {
	var html = '';
	
//	html += '<a href="mockup_publication.php?id=' + doc._id + '">';
	html += '<a href="references/' + doc._id + '">';
	if (doc.thumbnail) {
		html += '<img  width="60px;" style="background-color:white;" src="' + doc.thumbnail + '"/>';
	} else {
		html += '<div style="width:60px;height:80px;background-color:rgb(240,240,240);"></div>';
	}
	html += '</a>';
	
	$('#' + element_id).html(html);
}
