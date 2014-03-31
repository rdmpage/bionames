
// http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154

// http://stackoverflow.com/questions/5281007/bookmarklets-which-creates-an-overlay-on-page


//--------------------------------------------------------------------------------------------------
// http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154
// Test for presence of jQuery, if not, add it
if (!($ = window.jQuery)) { // typeof jQuery=='undefined' works too
    script = document.createElement( 'script' );
   script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'; 
    script.onload=releasetheKraken;
    document.body.appendChild(script);
} 
else {
    releasetheKraken();
}

//--------------------------------------------------------------------------------------------------
function rdmp_close(id)
{
	$('#' + id).remove();
}

//--------------------------------------------------------------------------------------------------



 
//--------------------------------------------------------------------------------------------------
function releasetheKraken() {
	// The Kraken has been released, master!
	// Yes, I'm being childish. Place your code here 
	//alert('kraken');
    
    
    var e = null;
    if (!$('#rdmpannotate').length) {
    
		// create the element:
		var e = $('<div id="rdmpannotate"></div>');
		
		// append it to the body:
		$('body').append(e);
		
		// style it:
		e.css({
			position: 'fixed',
			top: '0px',
			right: '0px',
			width: '300px',
			height: '400px',
			backgroundColor: 'white',
			color: 'black',
			'text-align':'left',
			'font-size': '12px',
			'font-weight': 'normal',
			'font-family': '\'Helvetica Neue\', Helvetica, Arial, sans-serif',
		'-webkit-box-shadow': '-7px 7px 10px 0px rgba(50, 50, 50, 0.5)',
		'-moz-box-shadow':    '-7px 7px 10px 0px rgba(50, 50, 50, 0.5)',
		'box-shadow':         '-7px 7px 10px 0px rgba(50, 50, 50, 0.5)' ,  
			'z-index':'200000'
		});
		
		$('#rdmpannotate').data("top", $('#rdmpannotate').offset().top);
	} else {
		e = $('#rdmpannotate');
	}
	

	// ×
	
	var html = '<span style="float:right;" onclick="rdmp_close(\'rdmpannotate\')">Close [x]</span>';
	html += '<div style="width:200px;font-size:120%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'
	+ '<span style="font-weight:bold;">' + window.document.title + '</span>' + '</div>';
	e.html(html);
	
	// Get identifier(s) from page
	
	// article with DOI
	// http://stackoverflow.com/questions/7524585/how-do-i-get-the-information-from-a-meta-tag-with-javascript

	var doi = '';
	var metas = document.getElementsByTagName('meta'); 

    for (i=0; i<metas.length; i++) { 
    	// Google Schlar tags
      if (metas[i].getAttribute("name") == "citation_doi") { 
         doi = metas[i].getAttribute("content"); 
      } 
      // Dublin Core
      if (metas[i].getAttribute("name") == "dc.Identifier") {
      	if (metas[i].getAttribute("scheme") == "doi") {
         doi = metas[i].getAttribute("content"); 
        }
      } 
      
    }
    
    if (doi != '') {
   		
   		// Format citation using CrossRef services 
		$.ajax({
			url: 'http://search.crossref.org/citation?format=apa&doi=' 
			+ encodeURIComponent('http://dx.doi.org/' + doi) + '&callback=?',
			success: function(data){
				
					var html = '<div style="padding:20px;">';
					html += data;
					html += '</div>';
					e.html(e.html() + html);
					}				
			});
			
		// List any names we know about for this publication
		$.getJSON('http://bionames.org/api/publication/doi/'  + doi + '/names?callback=?',
			function(data){
				if (data.status == 200) {
					var html = '<ul>';
					for (var i in data.names) {
						html += '<li>' + '<a href="http://bionames.org/names/' + data.names[i].cluster + '" target="_new">' + data.names[i].nameComplete + '</a></li>';
					}
					html += '</ul>';
					e.html(e.html() + html);
				}
			});
		
   	}
	
	// ZooBank LSID
	var lsid = '';
	$('span[class="lsid"]').each(function(index){
		lsid = this.innerText;
	});
    if (lsid != '') {
   		e.html(e.html() + '<br/>lsid:' + lsid);
   	}

	// ION LSID
	// <a href="/ipni/plantNameByVersion.do?id=13501-1&amp;version=1.1.2.1.1.3&amp;output_format=lsid-metadata&amp;show_history=true">TCS-RDF format</a>
	lsid = '';
	$('a[href^="lsidres:"]').each(function(index){
		lsid = this.href.replace(/lsidres:/, '');
	});
    if (lsid != '') {
   		e.html(e.html() + '<br/>lsid:' + lsid);
   	}
   	
   	// IPNI LSID
	lsid = '';
	$('a[href^="/ipni/plantNameByVersion.do"]').each(function(index){
		var pattern = /\/ipni\/plantNameByVersion.do\?id=(\d+-\d)/;
		var hit = pattern.exec(this.href);
		if (hit) {
			lsid = 'urn:lsid:ipni.org:names:' + hit[1];
		}
	});
    if (lsid != '') {
   		e.html(e.html() + '<br/>lsid:' + lsid);
   	}
   	
   	// EOL
	var pattern = /eol.org\/pages\/(\d+)/;
	
	hit = pattern.exec(window.location.href);
	if (hit) {
		$.getJSON('http://bionames.org/api/api_eol.php?id=' + hit[1] + '&publications&callback=?',
			function(data){
				if (data.status == 200) {
					var html = '<div style="padding:20px;">';
					for (var i in data.results) {
						
						if (data.results[i].thumbnail) {
							html += '<div style="border:1px solid rgb(192,192,192);float:right;"><img src="' + data.results[i].thumbnail + '" width="60" /></div>';
						}
						
						//html += data.results[i].title;
						html += data.results[i].formatted_citation;
						html += '<a href="http://bionames.org/references/' + data.results[i]._id + '" target="_new">View reference in BioNames</a>';
					}
					html += '</div>';
					e.html(e.html() + html);
				}
			});
	}
   	
   	
	

	// GBIF taxon
	pattern = /gbif.org\/species\/(\d+)/;
	
	hit = pattern.exec(window.location.href);
	
	if (hit) {
		$.getJSON('http://bionames.org/api/taxon/gbif/' + hit[1] + '/thumbnail/image?callback=?',
			function(data){
				if (data.status == 200) {
					var html = '<div>';
					if (data.thumbnails != 0) {
						var n = Math.min(6, data.thumbnails.length);
						for (var i = 0; i < n; i++) {
							html += '<img style="padding:4px;" src="' + data.thumbnails[i] + '" />';
						}
					}
					html += '</div>';
					html += '<span>Images from EOL</span>';
					e.html(e.html() + html);
				}
			});
	}
	
	// GBIF occurrence
	var pattern = /gbif.org\/occurrence\/(\d+)/;
	
	hit = pattern.exec(window.location.href);
	
	if (hit) {
		$.getJSON('http://api.gbif.org/v0.9/occurrence/' + hit[1] + '?callback=?',
			function(data){
				if (data.key == hit[1]) {
					var html = '<div style="text-align:left;">';
					html += '<div>' + data.institutionCode + ' ' + data.catalogNumber + '</div>';
					html += '<span>[' + data.latitude + ',' + data.longitude + ']</span>';
					if (data.longitude && data.latitude) {
						html += '<img src="http://maps.googleapis.com/maps/api/staticmap?' 
							+ 'size=300x100&zoom=6&maptype=terrain&markers=size:mid|' 
							+  data.latitude + ',' + data.longitude + '&sensor=false'
							+ '" />';
					}
					html += '</div>';
					e.html(e.html() + html);
				}
			});
	}
	


}

$(window).scroll(function(){
	var scrollTop = $(window).scrollTop();
    $('#rdmpannotate').css({'position': 'fixed', 'top': '0'}); 

});