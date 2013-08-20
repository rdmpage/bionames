<?php

require_once(dirname(__FILE__) . '/treemap.php');

$user_path = '';
if (isset($_GET['q']))
{
	$user_path = $_GET['q'];
	
	// clean
	
	$user_path = preg_replace('/^\//', '', $user_path);
	$user_path = preg_replace('/\/$/', '', $user_path);
}

//--------------------------------------------------------------------------------------------------
// load static file defining ION groups
function get_groups()
{
	$filename = dirname(__FILE__) . '/data/groups.json';
	$json = file_get_contents($filename);
	$groups = json_decode($json);
	
	$nodes = array();
	
	foreach ($groups->rows as $row)
	{
		$group = $row->key[0];
		$child_name = array_pop($group);
		$path = join('/', $group);
		
		if (!isset($nodes[$path]))
		{
			$nodes[$path] = array();
		}
		$child = new stdclass;
		$child->label = $child_name;
		$child->count = $row->value;
		
		$nodes[$path][] = $child;
	}	


	return $nodes;
}

//--------------------------------------------------------------------------------------------------
$g = explode('/', $user_path);
$this_node = $g[count($g) - 1];

// get ION groups
$nodes = get_groups();

//print_r($nodes);

// TreeMap data
$items = array();

// Is this group a leaf or not?
if (count($nodes[$user_path]) == 0)
{
	$group = $g;
	$child_name = array_pop($group);
	$path = join('/', $group);
	
	//echo $path;

	$i = new Item(
		0, 
		$this_node,
		1,
		true
		);
	array_push($items, $i);
}
else
{	
	foreach ($nodes[$user_path] as $node)
	{
		$link = $user_path;
		if ($user_path != '')
		{
			$link .= '/';
		}
		$link .= $node->label;
	
	
		$i = new Item(
			log10($node->count + 1), 
			$node->label,
			1,
			false,
			$link
			);
		array_push($items, $i);
	}
}



//--------------------------------------------------------------------------------------------------
// Display

?>
<!DOCTYPE html>
<html>
<head>
	<base href="http://bionames.org/" /><!--[if IE]></base><![endif]-->
	<title>Timeline</title>
	
	<!-- standard stuff -->
	<meta charset="utf-8" />
	<?php require 'stylesheets.inc.php'; ?>
	<?php require 'javascripts.inc.php'; ?>
	<?php require 'uservoice.inc.php'; ?>

	<script src="js/publication.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<style>
	#chart_container {
			position: relative;
			font-family: Arial, Helvetica, sans-serif;
	}
	#chart {
			position: relative;
			left: 40px;
	}
	#y_axis {
			position: absolute;
			top: 0;
			bottom: 0;
			width: 40px;
	}
	
  .thumbnail_blank {
  float: left;
  width:60px;
  height:80px;
  background-color: #fafafa;
  border: 1px solid #f2f2f2;
  border-radius: 2px;

}
	
	</style>

	<script>
	google.load("visualization", "1", {packages:["corechart"]});

	var datatable = null;
	
	
	function list_group_year(group, year)
	{
		$('#details').html('Thinking...');
		$.getJSON('http://bionames.org/bionames-api/api_timeline.php?group=' + encodeURIComponent(JSON.stringify(group)) + '&year=' + year + '&callback=?',
			function(data){
				if (data.status == 200)
				{
					var html = '';
					var ids = [];
					html += '<h4>Publications with new names for ' + year + '</h4>';
					
					html += '<div style="display:none;">';
					html += '<table>';
					for (var i in data.clusters)
					{
						html += '<tr>';
						html += '<td>' + data.clusters[i].nameComplete + '</td>';
						html += '<td>';
						
						html += '<div>';
						if (data.clusters[i].publishedInCitation)
						{
							if (ids.indexOf(data.clusters[i].publishedInCitation[0]) === -1)
							{
								ids.push(data.clusters[i].publishedInCitation[0]);
							}
							html += '<a href="timeline.html#id' + data.clusters[i].publishedInCitation[0] + '">View</a>';
						}
						if (data.clusters[i].publication)
						{
							html += data.clusters[i].publication[0];
						}
						html += '</div>';
						html += '</td>';
						html += '</tr>';
					}
					html += '</table>';
					html += '</div>';
					
					for (var id in ids) {
						html += '<div id="id' + ids[id] + '">' + ids[id] + '</div>';
					}
					
					$('#details').html(html);
					
										
						for (var id in ids) {
							display_publications(ids[id]);
							//display_snippets(ids[id]);
						}
					
					

				}
			});
		
	}

	function timeline (group)
	{
		//alert('http://bionames.org/bionames-api/api_timeline.php?group=' + encodeURIComponent(JSON.stringify(group)));
		$.getJSON('http://bionames.org/bionames-api/api_timeline.php?group=' + encodeURIComponent(JSON.stringify(group)) + '&callback=?',
			function(data){
				if (data.status == 200)
				{
					$('#chart').html('');
						var chart_data = [];
						chart_data.push(new Array('Year','Count'));		
						
						for (var year in data.years)
						{						
							chart_data.push(new Array(year, data.years[year]));
						}
						// $("#chart").height(100);
						
						var options = { title: name };
						var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
						datatable = google.visualization.arrayToDataTable(chart_data);
        				chart.draw(datatable, options);
        				
        				google.visualization.events.addListener(chart, 'select', 
        					function() {
        						var selection = chart.getSelection();
        						var item = selection[0];
  								//$('#details').html(JSON.stringify(group) + ' ' + 'year=' + datatable.getFormattedValue(item.row,0) + ' ' + datatable.getFormattedValue(item.row,item.column));
  								
  								list_group_year(group, datatable.getFormattedValue(item.row,0));
  							});
				
				
				
					//$('#chart').html('');
					/*
					var html = '';
					for (var year in data.years)
					{
						html += year + ' ' + data.years[year] + '<br />';
					}
					$('#timeline').html(html);
					*/
					
					/*
					var years = [];
					for (var year in data.years)
					{
						var item = {};
						item.x = parseInt(year);
						item.y = parseInt(data.years[year]);
						years.push(item);
					}

					
					var graph = new Rickshaw.Graph( {
							element: document.querySelector("#chart"),
							width: 580,
							height: 250,
							series: [ {
									color: 'steelblue',
									data: years
							} ]
					} );	
	
					graph.render();
					*/
					
					/*
					var years = [];
					years.push(['Years','Names']);
					for (var year in data.years)
					{
						var item = [];
						item.push(parseInt(year));
						item.push(parseInt(data.years[year]));
						years.push(item);
					}
					
					
					  google.load("visualization", "1", {packages:["corechart"]});
					  google.setOnLoadCallback(drawChart);
					  
				  function drawChart() {
					var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);
			
					var options = {
					  title: 'Names'
					};
			
					var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
					chart.draw(data, options);
				}
				*/
					
				}
			});
	}
	
	

function selectHandler(e) {
  alert('A table row was selected');
}	
</script>

</head>

<body>
	<?php require 'analyticstracking.inc.php'; ?>
	<?php require 'navbar.inc.php'; ?>

	<div class="container-fluid">

<?php

echo '<h2>Numbers of new names (any rank)</h2>';
echo '<p>Click on cell in treemap to drill down. Click on any column in timeline (right) to see list of publication for that year.</p>';

echo '<div class="row">';
// Breadcrumbs for navigation (last node is current page)
$n = count($g);
$breadcrumb = '';
echo '<ul class="breadcrumb">';
for ($i = 0; $i < $n-1; $i++)
{
	// path
	$breadcrumb = $breadcrumb . '/' . $g[$i];
	// display
	echo '<li><a href="timeline' . $breadcrumb . '">' . $g[$i] . '</a><span class="divider">/</span></li>';
}
echo '<li class="active">' . $g[$i] . '</li>';
echo '</ul>';

echo '</div>';

echo '<div class="row-fluid">
  <div class="span4">';

// Treemap

// Treemap bounds
$tm_width = 400;
$tm_height = 400;
$r = new Rectangle(0,0,$tm_width,$tm_height);

// Compute the layout
splitLayout($items, $r);

// Use a colour gradient to colour cells
$theColorBegin = 0x006600;
$theColorEnd = 0x000066;

$theR0 = ($theColorBegin & 0xff0000) >> 16;
$theG0 = ($theColorBegin & 0x00ff00) >> 8;
$theB0 = ($theColorBegin & 0x0000ff) >> 0;

$theR1 = ($theColorEnd & 0xff0000) >> 16;
$theG1 = ($theColorEnd & 0x00ff00) >> 8;
$theB1 = ($theColorEnd & 0x0000ff) >> 0;
  

// Enclose treemap in a DIV that has position:relative. The cells themselves have position:absolute.
// Note also that the enclosing DIV has the same height as the treemap, so that elements that follow
// the treemap appear below the treemap (rather than being obscured).
echo '<div style="color:white;font-family:Arial;position:relative;font-size:10px;height:' . $tm_height . 'px;margin-left:20px">';
$theNumSteps = count($items);
$count = 0;
foreach ($items as $i)
{
	if ($i->link != '')
	{
		echo '<a href="timeline/' . $i->link . '" style="color:white;">';
	}


	// Note that each treemap cell has position:absolute
	echo '<div id="div' . $i->id . '" class="cell" style="opacity:0.6;filter:alpha(opacity=60);position: absolute; overflow:hidden;text-align:center;';
	echo ' left:' . $i->bounds->x . 'px;';
	echo ' top:' . $i->bounds->y . 'px;';
	echo ' width:' . $i->bounds->w. 'px;';
	echo ' height:' . $i->bounds->h . 'px;';
	echo ' border:1px solid white;';
	
	// Background colour
    $theR = interpolate($theR0, $theR1, $count, $theNumSteps);
    $theG = interpolate($theG0, $theG1, $count, $theNumSteps);
    $theB = interpolate($theB0, $theB1, $count, $theNumSteps);
    $theVal = ((($theR << 8) | $theG) << 8) | $theB;

    printf("background-color: #%06X; ", $theVal);
	echo '" ';
    

	echo ' >';
	
	// Text is taxon name, plus number of leaf descendants
	// Note that $number[$count] is log (n+1)
	$tag = $i->label;
	
	if ($i->size > 0)
	{
		$tag .= ' ' . number_format(pow(10, $i->size) - 1);
	}
			
	// format the tag...
	// 1. Find longest word
	$words = preg_split("/[\s]+/", $tag);
	
	$max_length = 0;
	foreach($words as $word)
	{
		$max_length = max($max_length, strlen($word));
	}
	
	// Font upper bound is proportional to length of longest word
	$font_height = $i->bounds->w / $max_length;
	$font_height *= 1.2;
	if ($font_height < 10)
	{
		$font_height = 10;
	}

	// text			
	echo '<span style="font-size:' . $font_height . 'px;">' . $tag . '</span>';
	
	echo '</div>';
	
	if ($i->link != '')
	{
		echo '</a>';
	}
	
	echo "\n";

	$count++;
}
echo '</div>';

echo '   </div> <!-- span -->';

echo '   <div class="span7">';
echo '      <div id="chart" style="width: auto; height: 400px;">Loading timeline...</div>';
echo '   </div> <!-- span -->';

echo '</div> <!-- row -->';

echo '<div class="row">';
echo '   <div id="details" class="span12" ></div>';
echo '</div>';

echo '<script> timeline(' . json_encode($g) . '); </script>';

echo '</div>';

echo '</body>
</html>';



?>