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


include ( "jpgraph.php" );
include ( "jpgraph_line.php" );

// Some data
$ydata  = array(11,3, 8,12,5 ,1,9, 13,5,7 );

// Create the graph. These two calls are always required
$graph  = new Graph(350, 250,"auto");    
$graph->SetScale( "textlin");

// Create the linear plot
$lineplot =new LinePlot($ydata);
$lineplot ->SetColor("blue");

// Add the plot to the graph
$graph->Add( $lineplot);

// Display the graph
$graph->Stroke();
?> 
