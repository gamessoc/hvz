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


session_start();

require_once( "includes/hvz_includes.inc.php" );

$title = "Registration";

$b = <<<ZZZ
<p>Thanks for Registering! You can now <a href="login.php">Login</a>.</p>

<p><b>Note:</b> You are <b>NOT IN A GAME YET</b>, to join a game go to "View Games"
and find the game you would like to join, you can then click on the Join Game
link on the game page, if registration is open for the game.</p>

ZZZ;

HVZ_make_page( $title, $b, 0 );

?>
