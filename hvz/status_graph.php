<?php

/************************************************************************

Copyright 2008 Dana E. Cartwright IV

The author can be reached for comments or suggestions at
decartwright@gmail.com

*************************************************************************

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
*********************************************************************/


require_once( "includes/hvz_includes.inc.php" );

require( "jpgraph/jpgraph.php" );
require( "jpgraph/jpgraph_line.php" );


$db = new DB();
$db->connect();

$gid = $_GET['gid'];

$status_data = $db->get_status_data( $gid );

if( count($status_data) < 2 )
    exit();  // we are an image, we won't bother erroring. We shouldn't be called in this case

$xdata = array();
$human_data = array();
$zombie_data = array();
$deceased_data = array();

$initial_time = FALSE;
foreach( $status_data as $data )
{
    if( !$initial_time )
        $initial_time = strtotime( $data['logged'] );
        
    $current_time = strtotime( $data['logged'] );
    $time_diff = $current_time - $initial_time;
    $hour_diff = round( $time_diff / 3600 );
    
    array_push( $xdata, $hour_diff );
    
    array_push( $human_data, $data['humans'] );
    array_push( $zombie_data, $data['zombies'] );
    array_push( $deceased_data, $data['deceased'] );
}

$graph = new Graph( 450, 300, "auto" );    
$graph->SetScale( "textlin" ); 

$human_plot = new LinePlot( $human_data, $xdata );
$human_plot->SetColor( "blue" );
$human_plot->SetLegend( "Resistance" );
$human_plot->SetWeight( 2 );

$zombie_plot = new LinePlot( $zombie_data, $xdata );
$zombie_plot->SetColor( "red" );
$zombie_plot->SetLegend( "Horde" );
$zombie_plot->SetWeight( 2 );

$deceased_plot = new LinePlot( $deceased_data, $xdata );
$deceased_plot->SetColor( "gray5" );
$deceased_plot->SetLegend( "Deceased" );
$deceased_plot->SetWeight( 2 );

$graph->Add( $human_plot );
$graph->Add( $zombie_plot );
$graph->Add( $deceased_plot );

$graph->img->SetMargin( 45, 114, 30, 45 );

$graph->title->Set( "Graph of player count vs. Time" );

$graph->xaxis->SetTitle( "Hours since start", "middle" );
$graph->xaxis->SetTitleMargin( 12 );
if( count($xdata) < 100 ) // triple digits screw with things
    $graph->xaxis->SetTextTickInterval( ceil(count($xdata) / 18) );
else
    $graph->xaxis->SetTextTickInterval( ceil(count($xdata) / 15) );

$graph->yaxis->SetTitle( "Player count", "middle" );
$graph->yaxis->SetTitleMargin( 29 );
$graph->yaxis->SetLabelFormat( "%d" );

$graph->title->SetFont(FF_FONT2 ,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1 ,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1 ,FS_BOLD); 

$graph->legend->Pos( 0.02, 0.15 );

$graph->Stroke();

?>
