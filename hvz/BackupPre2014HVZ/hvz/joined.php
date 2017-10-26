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

$db = new DB();
$db->connect();

$title = "Game Joined";

$b = '';

if( isset($_GET['gid']) && isset($_GET['pin']) )
{
    $game_info = $db->get_game_info( $_GET['gid'] );
    
    $game_title = $game_info['title'];
    $pin = $_GET['pin'];
}

$b .= <<<ZZZ
<p>You have succesfully joined the game "$game_title". Your "kill PIN" for this
game is as written below:</p>
<p><b>$pin</b></p>
<p>Write this number and your name on an index card or other small piece of paper
and keep it with you at all times while playing. When you are killed you will give
this card to the player that killed you. Reporting a kill <b>requires</b> the
killed player's PIN to report it.</p>
<p>You can view the current status of the game <a href="view_game.php?id=${_GET['gid']}">here</a>.</p>
ZZZ;


HVZ_make_page( $title, $b, 0 );

?>
