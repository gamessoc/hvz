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

$title = "Games";

$b = '';

$games_list = $db->get_games_list();

$b .= <<<ZZZ
<table style="border-collapse: collapse; width: 90%;">
<tr>
    <td class="table-header">Game</td>
    <td class="table-header">Status</td>
    <td class="table-header">Registration</td>
    <td class="table-header">Players</td>
</tr>
ZZZ;
       
$on_off = 'on';
foreach( $games_list as $game_id )
{
    $b .= "<tr>\n";
    
    $game_info = $db->get_game_info( $game_id );
    
    $b .= '<td class="table-cell-'.$on_off.'"><a href="view_game.php?id='.$game_id.'">'.$game_info['title'].'</a></td>'."\n";
    
    $b .= '<td class="table-cell-'.$on_off.'">'.$game_info['status'].'</td>'."\n";
    
    $b .= '<td class="table-cell-'.$on_off.'">'.$game_info['registration'].'</td>'."\n";
    
    $player_list = $db->get_players_info( $game_id );
    $num_players = count( $player_list );
    foreach( $player_list as $player )
        if( $player['team'] == 'Kicked' )
            $num_players--;
    
    $b .= '<td class="table-cell-'.$on_off.'">'.$num_players.'</td>'."\n";
    
}

$b .= "</table>\n";

HVZ_make_page( $title, $b, 0 );

?>
