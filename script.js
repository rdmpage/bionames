// Bookmarklet

// http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154

// http://stackoverflow.com/questions/5281007/bookmarklets-which-creates-an-overlay-on-page

var observer = null;

var debug = false;
//debug = true;

rdmp_init();

//----------------------------------------------------------------------------------------
function rdmp_init() {
  // http://code.tutsplus.com/tutorials/create-bookmarklets-the-right-way--net-18154
  // Test for presence of jQuery, if not, add it
  if (!($ = window.jQuery)) { // typeof jQuery=='undefined' works too
    // Create a script tag to load the bookmarklet
    script = document.createElement('script');
    script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js';
    script.onload = releasetheKraken;
    document.body.appendChild(script);
  }
  else {
    releasetheKraken();
  }
}

//----------------------------------------------------------------------------------------
function rdmp_close(id) {
  $('#' + id).remove();
}



//--------------------------------------------------------------------------------------------------
function releasetheKraken() {
  // The Kraken has been released, master!
  // Yes, I'm being childish. Place your code here 
  //alert('kraken');
  
  var e = null;
  if (!$('#pidannotate').length) {
  
     // create the element:
    e = $('<div class="pidannotate" id="pidannotate"></div>');

    // append it to the body:
    $('body').append(e);
    
    var styles = `
    	.pidannotate {
			position:    		fixed;
			top:         		0px;
			right:       		0px;
			width:       		50%;
			height:      		100vh;
			padding:     		20px;
			background-color: 	white;
			color:       		black;
			text-align:  		left;
			font-size:   		12px;
			font-weight: 		normal;
			font-family: 		Helvetica, Arial, sans-serif;
			box-shadow:  		-5px 5px 5px 0px rgba(50, 50, 50, 0.3);
			z-index:     		200000;
			overflow-y:			auto;
    	}
 
 .materialsCitation  { background-color: rgb(251,242,64); }
.collectingCountry { background-color: rgb(223,1289,32); }
.collectingRegion { background-color: rgb(242,156,36); }
.collectingCountry { background-color: rgb(242,122,29); }
.collectingMunicipality { background-color: rgb(192,192,192); }
.locationDeviation { background-color: rgb(245,233,142); }
.collectingDate  { background-color: rgb(164,238,163); }
.collectingMethod { background-color: rgb(193,238,196); }
.collectorName { background-color: rgb(143,234,139); }
.specimenCount  { background-color: rgb(252,252,137); }
.specimenType { background-color: rgb(192,192,192); }
.typeStatus { background-color: rgb(190,192,242); }
.collectionCode { background-color: rgb(123,134,234); }
.specimenCode { background-color: rgb(155,164,231); }

.caption { border:1px solid black; padding:1em;border-radius: 4px; background-color: #FFFC79; }



.geoCoordinate { background-color: rgb(186,252,136); }
.location { background-color: rgb(254,191,132); }
.quantity { background-color: rgb(125,172,252); }
.specimenCode  { background-color: rgb(255,247,136); }
.date  { background-color: rgb(241,135,252); }
.elevation  { background-color: rgb(250,128,159); }

.accessionNumber  { background-color: red; color:white; }


/* https://www.w3schools.com/css/css_tooltip.asp */

/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
  
  bottom: 100%;
  left: 50%; 
  margin-left: -60px; /* Use half of the width (120/2 = 60), to center the tooltip */
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}

				
    `;
    
    var styleSheet = document.createElement("style")
	styleSheet.type = "text/css"
	styleSheet.innerText = styles
	document.head.appendChild(styleSheet)

    $('#pidannotate').data("top", $('#pidannotate').offset().top);
   }
  else {
    e = $('#pidannotate');
  }

  var html = '<span style="float:right;" onclick="rdmp_close(\'pidannotate\')">Close [x]</span>';
  
  // Title
  html += '<h1>' + window.document.title + '</h1>';
  html += '<div id="output"></div>';
  e.html(html);
  
  var guid = '';

	var pattern = /html\/([A-Z0-9]+)/;	
	var m  = pattern.exec(window.location.href);
    if (m)
    {
    	guid = m[1];
    }
 
    	console.log(guid);
    	
    	var xml = loadXMLDoc("https://tb.plazi.org/GgServer/xml/" + guid);
    	
var xsltext = `<?xml version='1.0' encoding='utf-8'?>
<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform' xmlns:mods="http://www.loc.gov/mods/v3" exclude-result-prefixes="mods">

<xsl:output method='html' version='1.0' encoding='utf-8' indent='yes'/>



<xsl:template match="/">
<!--		<style>
.materialsCitation  { background-color: rgb(251,242,64); }
.collectingCountry { background-color: rgb(223,1289,32); }
.collectingRegion { background-color: rgb(242,156,36); }
.collectingCountry { background-color: rgb(242,122,29); }
.collectingMunicipality { background-color: rgb(192,192,192); }
.locationDeviation { background-color: rgb(245,233,142); }
.collectingDate  { background-color: rgb(164,238,163); }
.collectingMethod { background-color: rgb(193,238,196); }
.collectorName { background-color: rgb(143,234,139); }
.specimenCount  { background-color: rgb(252,252,137); }
.specimenType { background-color: rgb(192,192,192); }
.typeStatus { background-color: rgb(190,192,242); }
.collectionCode { background-color: rgb(123,134,234); }
.specimenCode { background-color: rgb(155,164,231); }

.caption { border:1px solid black; padding:1em;border-radius: 4px; background-color: #FFFC79; }



.geoCoordinate { background-color: rgb(186,252,136); }
.location { background-color: rgb(254,191,132); }
.quantity { background-color: rgb(125,172,252); }
.specimenCode  { background-color: rgb(255,247,136); }
.date  { background-color: rgb(241,135,252); }
.elevation  { background-color: rgb(250,128,159); }

.accessionNumber  { background-color: red; color:white; }


/* https://www.w3schools.com/css/css_tooltip.asp */

/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
  
  bottom: 100%;
  left: 50%; 
  margin-left: -60px; /* Use half of the width (120/2 = 60), to center the tooltip */
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}


	</style>	 -->	
	<xsl:apply-templates />
</xsl:template>

<xsl:template match="treatment">
	<xsl:apply-templates />
</xsl:template>

<xsl:template match="mods:mods">
	<!-- eat all the bibliographic metadata -->
</xsl:template>

<xsl:template match="subSubSection">
<section>
	<xsl:apply-templates />
</section>
</xsl:template>



<xsl:template match="paragraph">
	<p>
	<!-- <xsl:value-of select="."/> -->
	<xsl:apply-templates />
	</p>
</xsl:template>

<xsl:template match="heading">
	<h1>
	<xsl:apply-templates />
	</h1>
</xsl:template>



<xsl:template match="taxonomicName">
<b>
	<xsl:apply-templates />
</b>

</xsl:template>



<xsl:template match="treatmentCitationGroup">

<span>
	<xsl:apply-templates />
</span>

</xsl:template>

<xsl:template match="figureCitation">

<span>
	<xsl:apply-templates />
</span>

</xsl:template>

<xsl:template match="caption">
<div class="caption">
	<img>
		<xsl:attribute name="src">
			<xsl:value-of select="@httpUri" />
		</xsl:attribute>
		<xsl:attribute name="width">
			<xsl:text>100%</xsl:text>
		</xsl:attribute>
		
	</img>


	<xsl:apply-templates />
</div>

</xsl:template>


<xsl:template match="materialsCitation">
<div class="materialsCitation">
	<xsl:apply-templates />
</div>
</xsl:template>



<xsl:template match="collectingRegion">
<span class="tooltip collectingRegion">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>
</xsl:template>


<xsl:template match="collectingMunicipality">
<span class="tooltip collectingMunicipality">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>
</xsl:template>

<xsl:template match="collectingCounty">
<span class="tooltip collectingCounty">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>

</xsl:template>

<xsl:template match="collectingCountry">
<span class="tooltip collectingCountry">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>

	<xsl:apply-templates />
</span>

</xsl:template>


<xsl:template match="collectorName">
<span class="tooltip collectorName">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>
</xsl:template>

<xsl:template match="date">
<span class="tooltip date">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>

</xsl:template>

<xsl:template match="location">
<span class="tooltip location">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
</span>

</xsl:template>

<xsl:template match="collectionCode">
<span class="tooltip collectionCode">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>

<xsl:template match="specimenCode">
<span class="tooltip specimenCode">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>



<xsl:template match="specimenCount">
<span class="tooltip specimenCount">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
	<xsl:apply-templates />
	</span>
</xsl:template>

<xsl:template match="quantity">
<span class="tooltip quantity">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>

<xsl:template match="typeStatus">
<span class="tooltip typeStatus">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>

<xsl:template match="geoCoordinate">
<span class="tooltip geoCoordinate">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>

<xsl:template match="accessionNumber">
<span class="tooltip accessionNumber">
 	<span class="tooltiptext"><xsl:value-of select="local-name()"/></span>
		<xsl:apply-templates />
	</span>
</xsl:template>





<xsl:template match="emphasis">
<i>
	<xsl:apply-templates />
</i>
</xsl:template>




</xsl:stylesheet>
`;

var parser = new DOMParser();

xsl = parser.parseFromString(xsltext,"text/xml");




  xsltProcessor = new XSLTProcessor();
  xsltProcessor.importStylesheet(xsl);
  resultDocument = xsltProcessor.transformToFragment(xml, document);
  document.getElementById("pidannotate").appendChild(resultDocument);    	




}

//----------------------------------------------------------------------------------------
function loadXMLDoc(filename)
{
	xhttp = new XMLHttpRequest();
	xhttp.open("GET", filename, false);
	try {xhttp.responseType = "msxml-document"} catch(err) {} // Helping IE11
	xhttp.send("");
	return xhttp.responseXML;
}

//----------------------------------------------------------------------------------------
/* Can't use jquery at this point because it might not have been loaded yet */
// https://stackoverflow.com/a/17494943/9684

var startProductBarPos = -1;

window.onscroll = function() {
  var bar = document.getElementById('pidannotate');
  if (startProductBarPos < 0) startProductBarPos = findPosY(bar);

  if (pageYOffset > startProductBarPos) {
    bar.style.position = 'fixed';
    bar.style.top = 0;
  }
  else {
    bar.style.position = 'fixed';
  }

};

function findPosY(obj) {
  var curtop = 0;
  if (typeof(obj.offsetParent) != 'undefined' && obj.offsetParent) {
    while (obj.offsetParent) {
      curtop += obj.offsetTop;
      obj = obj.offsetParent;
    }
    curtop += obj.offsetTop;
  }
  else if (obj.y)
    curtop += obj.y;
  return curtop;
}