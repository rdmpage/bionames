function has_doi(data)
{
	var doi = '';
	if (data.identifier)
	{
	  for (var j in data.identifier)
	  {
	    switch (data.identifier[j].type)
	    {	        
	      case "doi":
	        doi = data.identifier[j].id;
	        break;
	        
	      default:
	        break;
	    }
	  }  
	}
	return doi;
}

/* Display a snippet of information about an object */

function show_snippet (element_id, doc) {
	var html = '';
	
	html += '<div class="snippet">';
	
	switch (doc.type) {
		case 'generic':
			// Unparsed publication?
			html += '<a href="references/' + doc._id + '">';
			html += '<div class="details">';
			html += '<div class="metadata">';
			html += doc.citation_string;
			html += '</div><!-- end metadata -->';
			html += '</a>';
			html += '</div>';
			break;
	
		case 'article':
		case 'book':
		case 'chapter':		
			// Publication snippet	
//			html += '<a href="mockup_publication.php?id=' + doc._id + '">';
			html += '<a href="references/' + doc._id + '">';
			if (doc.thumbnail) {
				html += '<img class="thumbnail" src="' + doc.thumbnail + '"/>';
			} else {
				html += '<div class="thumbnail_blank">';
				doi = has_doi(doc);
				if (doi != '')
				{
					html += '<img src="images/doi16x16.png" style="float:right;"/>';
				}				
				html += '</div>';
			}
						
			html += '<div class="details">';
			if (doc.title)
			{
				html += '<div class="title">' + doc.title + '</div>';
			}
			
			html += '<div class="metadata"><!-- begin metadata -->';
			
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
      // html += '</div>';
      // 
      // html += '<div>';
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
				html += '<ul class="identifier">';
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
		
			
			html += '</div><!-- end metadata -->';
			html += '</a>';
			html += '</div>';
			break;
			
		case 'taxonConcept':
 //     var link = "mockup_concept.php?id=" + doc._id;
      var link = "taxa/" + doc._id;
      
      html += ('<div id="thumb-' + element_id + '" class="thumbnail concept-thumb"></div>');

 //   	$.getJSON("http://bionames.org/bionames-api/taxon/" + doc._id + "/thumbnail?callback=?", function(data){
    	$.getJSON("api/taxon/" + doc._id + "/thumbnail?callback=?", function(data){
 			if (data.status == 200)
  			{	
          if(data.thumbnails && data.thumbnails.length > 0) {
            $('<a href="'+link+'"><img src="'+ data.thumbnails[0] +'"/></a>').appendTo($('#thumb-' + element_id));
          }
        }
    	});
      
      
			html += '<div class="details">';
			html += '<a href="' + link + '">';
			if (doc.canonicalName)
			{
				html += '<div class="title">';
				html += doc.canonicalName;
				
				if (doc.author) {
					html += ' ' + doc.author;
				}
				
				html += '</div>';
			} else {
				html += '<div class="title">';
				html += doc.scientificName;
				html += '</div>';
			}			
			html += '<!-- metadata -->';
			
			if (doc._id.match(/gbif/)) {
          html += '<div class="metadata gbif">';
					html += 'According to GBIF ' + doc._id.replace(/gbif\//, '');
					html += '</div>';
          html += '<div class="logo"><img src="images/logo-gbif.png"/></div>';
			}									
			if (doc._id.match(/ncbi/)) {
					html += '<div class="metadata ncbi">';
          html += 'According to NCBI ' + doc._id.replace(/ncbi\//, '');
					html += '</div>';
          html += '<div class="logo"><img src="images/logo-ncbi.png"/></div>';
			}									
			
			
			html += '</a>';
			html += '</div>';
			break;
			
		case 'nameCluster':
			html += '<div class="details_wide">';
//			html += '<a href="mockup_taxon_name.php?id=' + doc._id + '">';
			html += '<a href="names/' + doc._id + '">';
			if (doc.nameComplete)
			{
				html += '<div class="title">';
				html += doc.nameComplete;
				
				if (doc.taxonAuthor) {
					html += ' ' + doc.taxonAuthor;
				}
				
				html += '</div>';
			}
			html += '<div class="metadata"><!-- metadata -->';
			
			if (doc.names)
			{
				html += '<ul class="identifier">';
				for (var i in doc.names)
				{
					html += '<li>' + doc.names[i].id + '</li>';
				}
				html += '</ul>';
			}
			
			if (doc.publication)
			{
				for (var i in doc.publication)
				{
					html += '<div>';
					html += doc.publication[i];
					html += '</div>';
				}
			}
			
			html += '</div><!-- end metadata -->';
			html += '</a>';
			html += '</div>';					
			break;
			
			
		default:
			break;
	}
	
	
	
	html += '<div style="clear:both;">';
	html += '</div>';
	
	$('#' + element_id).html(html);
}


/* Display snippet */
function display_snippets(id) {
//	$.getJSON("http://bionames.org/bionames-api/id/" + id + "?callback=?",
	$.getJSON("api/id/" + id + "?callback=?",
		function(data){
			if (data.status == 200)
			{		
				var element_id = 'id' + id;
				element_id = element_id.replace(/\//, '_');
				element_id = element_id.replace(/\-/g, '_');
				element_id = element_id.replace(/\./g, '_');
				
				//console.log(element_id);
				//alert(element_id);
				
				//$('#' + element_id).html(html);
				
				show_snippet(element_id, data);
			}
		});
}
