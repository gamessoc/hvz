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

$title = "Player Details";

$b = '';

$game_info = FALSE;
$user_info = FALSE;
if( isset($_GET['gid']) && isset($_GET['uid']) )
{
    $gid = $_GET['gid'];
    $db->check_zombie_starve( $gid );
    $game_info = $db->get_game_info( $gid );
    $players_info = $db->get_players_info( $gid );
    
    $uid = $_GET['uid'];
    $user_info = $db->get_user_info( $uid );
    
    $player_info = FALSE;
    foreach( $players_info as $player )
        if( $player['uid'] == $uid )
            $player_info = $player;
}

if( !$game_info || !$user_info || !$player_info )
{
    $b .= '<p class="error">Sorry, an error has occurred. If the problem persists please contact an admin.</p>' . "\n";
}
else
{
    if( $player_info['team'] == 'Horde' && $player_info['is_oz'] && $game_info['oz_status'] == 'Revealed' )
        $team_display = 'Horde (Original)';
    elseif( $player_info['team'] == 'Horde' && $player_info['is_oz'] )
        $team_display = 'Resistance';
    else
        $team_display = $player_info['team'];

    $b .= <<<ZZZ
<div class="status-area">
<table>
<tr>
    <td class="form-label">Game:</td>
    <td class="form-value"><a href="view_game.php?id=$gid">${game_info['title']}</a></td>
</tr>
<tr>
    <td class="form-label">Name:</td>
    <td class="form-value">${user_info['first_name']} ${user_info['last_name']}</td>
</tr>
<tr>
    <td class="form-label">Class Year:</td>
    <td class="form-value">${user_info['class_year']}</td>
</tr>
<tr>
    <td class="form-label">Team:</td>
    <td class="form-value">$team_display</td>
</tr>
ZZZ;

    if( $player_info['team'] == 'Resistance' || $team_display == 'Resistance' )
    {
        $survival_time = (int)$db->get_survival_time( $gid );
        $b .= <<<ZZZ
<tr>
    <td class="form-label">Survived For:</td>
    <td class="form-value">$survival_time Hours</td>
</tr>
ZZZ;
    }
    
    if( ($player_info['team'] == 'Horde' || $player_info['team'] == 'Deceased') && $team_display != 'Resistance' )
    {
        $killed_by = $db->get_killed_by( $gid, $uid );
        
        if( $killed_by && $killed_by != -1 )
        {
            $killed_by_info = $db->get_user_info( $killed_by );
            
            foreach( $players_info as $player )
                if( $player['uid'] == $killed_by )
                    $killed_by_player_info = $player;
            
            if( $game_info['oz_status'] == 'Hidden' && $killed_by_player_info['is_oz'] )
            {
                
                $b .= <<<ZZZ
<tr>
    <td class="form-label">Killed By:</td>
    <td class="form-value">---Hidden---</td>
</tr>
ZZZ;
            }
            else
            {        
                
                $b .= <<<ZZZ
<tr>
    <td class="form-label">Killed By:</td>
    <td class="form-value"><a href="player_details.php?gid=$gid&uid=$killed_by">${killed_by_info['first_name']} ${killed_by_info['last_name']}</a></td>
</tr>
ZZZ;
            }
        }
        else
        {
            $b .= <<<ZZZ
<tr>
<td class="form-label">Killed By:</td>
<td class="form-value">None</td>
</tr>
ZZZ;
        }
    }
    
    if( $player_info['team'] == 'Horde' && $team_display != 'Resistance'  )
    {
        $hours_left = (int)$db->zombie_hours_to_starve( $gid, $uid );
        $b .= <<<ZZZ
<tr>
    <td class="form-label">Starves In:</td>
    <td class="form-value">$hours_left Hours</td>
</tr>
ZZZ;
    }
    
    $b .= <<<ZZZ
</table>
</div>
ZZZ;


    if( $player_info['team'] == 'Horde' && $team_display != 'Resistance' )
    {
        $kills = $db->get_kills( $gid, $uid );
        
        $kill_count = 0;
        $assist_count = 0;
        $kill_list = '';
        $assist_list = '';
        foreach( $kills as $kill )
        {
            $killed_info = $db->get_user_info( $kill['killed_id'] );
            
            if( $kill['primary_kill'] )
            {
                $kill_count++;
                $kill_list .= <<<ZZZ
<tr>
    <td>
        <a href="player_details.php?gid=$gid&uid=${killed_info['uid']}">${killed_info['first_name']} ${killed_info['last_name']}</a>
    </td>
</tr>
ZZZ;
            }
            else 
            {
                $assist_count++;
                $assist_list .= <<<ZZZ
<tr>
    <td>
        <a href="player_details.php?gid=$gid&uid=${killed_info['uid']}">${killed_info['first_name']} ${killed_info['last_name']}</a>
    </td>
</tr>
ZZZ;
            }
        }

        $b .= <<<ZZZ
<hr>
<h3>Kills ($kill_count)</h3>
ZZZ;

        if( $kill_count == 0 )
        {
            $b .= "<p>This zombie has not made any kills yet.</p>";
        }
        else
        {
            $b .= "<table>\n";
            $b .= $kill_list;
            $b .= "</table>\n";
        }
            
        $b .= <<<ZZZ
<h3>Kill Assists ($assist_count)</h3>
ZZZ;

        if( $assist_count == 0 )
        {
            $b .= "<p>This zombie has not made any assists yet.</p>";
        }
        else
        {
            $b .= "<table>\n";
            $b .= $assist_list;
            $b .= "</table>\n";
        }        
        
    }
}


HVZ_make_page( $title, $b, 0 );

?>
