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

$title = "Game Info";

$b = '';

$game_info = FALSE;
if( isset($_GET['id']) )
{
    $db->check_zombie_starve( $_GET['id'] );
    $game_info = $db->get_game_info( $_GET['id'] );
    $players_info = $db->get_players_info( $_GET['id'] );
}

$humans = array();
$zombies = array();
$deceased = array();
foreach( $players_info as $player )
{
    if( $player['team'] == 'Resistance' )
        $humans[$player['uid']] = $player;
    elseif( $player['team'] == 'Horde' )
    {
        if( $player['is_oz'] && $game_info['oz_status'] != 'Revealed' )
            $humans[$player['uid']] = $player;
        else
            $zombies[$player['uid']] = $player;
    }
    elseif( $player['team'] == 'Deceased' )
        $deceased[$player['uid']] = $player;
}

// re-sort zombies by kills
$zombies_sort = array();
$zombies_temp = $zombies;
$max_kill_count = 0;
foreach( $zombies as $uid => $zombie )
{
    $kills = $db->get_kills( $game_info['id'], $uid );
    $kill_count = 0;
    foreach( $kills as $kill )
        if( $kill['primary_kill'] )
            $kill_count++;
            
    $zombies_sort[$uid] = $kill_count;
    
    if( $kill_count > $max_kill_count )
        $max_kill_count = $kill_count;
}
arsort( $zombies_sort );

$zombies_final_order = array();
// now sort within each kill number by time to starvation
$zombies_sort_segments = array();
for( $i = $max_kill_count; $i >= 0; $i-- )
{
    $zombies_sort_segments[$i] = array_keys( $zombies_sort, $i );
}
foreach( $zombies_sort_segments as $segment_array )
{
    $starve_times = array();
    foreach( $segment_array as $uid )
        $starve_times[$uid] = $db->zombie_hours_to_starve( $game_info['id'], $uid );
    arsort( $starve_times );
    
    foreach( $starve_times as $uid => $unused )
        array_push( $zombies_final_order, $uid );
}  
// assemble the final ordered array
$zombies = array();
foreach( $zombies_final_order as $uid )
{
    $zombies[$uid] = $zombies_temp[$uid];
}

// construct an array of the most recently killed players' uids
$sort_kill_time = array();
$combined = array_merge( $zombies, $deceased );
foreach( $combined as $player )
{
    $sort_kill_time[$player['uid']] = $player['infected'];
}
arsort( $sort_kill_time );
$latest_kills_info = array_slice( $sort_kill_time, 0, 6, TRUE );
$latest_kills = array_keys( $latest_kills_info );

// construct an array of the most recently killed players' uids
$deceased_sort_time = array();
foreach( $deceased as $player )
{
    $deceased_sort_time[$player['uid']] = $player['starved'];
}
arsort( $deceased_sort_time );
$latest_starves_info = array_slice( $deceased_sort_time, 0, 3, TRUE );
$latest_starves = array_keys( $latest_starves_info );


if( !$game_info )
{
    $b .= '<p class="error">Sorry, we could not find the game you were looking for.</p>' . "\n";
}
else
{
    // output game status
    $b .= <<<ZZZ

<div class="status-area">
<table>
<tr>
    <td class="form-label">Game:</td>
    <td class="form-value">${game_info['title']}</td>
</tr>
<tr>
    <td class="form-label">Status:</td>
    <td class="form-value">${game_info['status']}</td>
</tr>
<tr>
    <td class="form-label">Registration:</td>
    <td class="form-value">${game_info['registration']}</td>
</tr>
<tr>
    <td class="form-label">Zombies Starve After:</td>
    <td class="form-value">${game_info['zombie_starve_time']} Hours</td>
</tr>
<tr>
    <td class="form-label">Zombies Are Fed From A Kill By:</td>
    <td class="form-value">${game_info['zombie_feed_time']} Hours</td>
</tr>
<tr>
    <td class="form-label">Zombies Are Stunned For:</td>
    <td class="form-value">${game_info['zombie_stun_time']} Minutes</td>
</tr>
<tr>
    <td class="form-label">Original Zombie(s):</td>
    <td class="form-value">${game_info['oz_status']}</td>
</tr>
</table>
</div>
ZZZ;

    if( isset($_SESSION['uid']) )
    {
        $b .= <<<ZZZ
<hr>
<h3>Controls</h3>
ZZZ;
    }

    // display admin link?
    if( isset($_SESSION['uid']) )
    {
        $email = $_SESSION['uid'];
        $user_info = $db->get_user_info( $email );
            
        if( $user_info['admin'] == TRUE )
        {
            $b .= "<p><a href='admin_game.php?id=${game_info['id']}'>Administer This Game</a></p>";
        }
    }
    
    // display join game or report a kill link
    if( isset($_SESSION['uid']) && $game_info['status'] != 'Concluded' )
    {
        $user_in_game = FALSE;
        foreach( $players_info as $player )
            if( $player['uid'] == $_SESSION['uid'] )
                $user_in_game = TRUE;
                
        if( $user_in_game )
            $b .= "<p><a href='report_kill.php?gid=${game_info['id']}'><img src=\"./images/kill.png\" /></a></p>";
        elseif( $game_info['registration'] == 'Open' )
            $b .= "<p><a href='join_game.php?id=${game_info['id']}'><img src=\"./images/joinGame.png\" />  </a></p>";
    }
    elseif( $game_info['registration'] == 'Open' )
        $b .= "<p>Want to join this game? <a href='register.php'>Register</a> and <a href='login.php'>Login</a> first.</p>\n";
        
        

    if( !$players_info )
    {
        $b .= "<p>No players have currently joined this game.</p>\n";
    }
    else
    {
        $survival_time = (int)$db->get_survival_time( $game_info['id'] );
    
        // function to display each player, so we can interate the 3 different classes of user separately
        function display_player( $player )
        {
            global $b, $db, $game_info, $on_off, $survival_time;
        
            if( $player['team'] == 'Kicked' )
                continue;

            $b .= "<tr>\n";
            
            $user_info = $db->get_user_info( $player['uid'] );
            
            $b .= '<td class="table-cell-'.$on_off.'"><a href="player_details.php?gid='.$game_info['id'].'&uid='.$user_info['uid'].'">'.$user_info['first_name'].' '.$user_info['last_name'].'</a></td>'."\n";
            
            if( $player['team'] == 'Resistance' || ($player['is_oz'] && $game_info['oz_status'] != 'Revealed') )
            {
                $b .= '<td class="table-cell-'.$on_off.'">'.$survival_time.'</td>'."\n";
            }
            elseif( $player['team'] == 'Horde' && !($player['is_oz'] && $game_info['oz_status'] != 'Revealed') )
            {
                $kills = $db->get_kills( $game_info['id'], $player['uid'] );
                $kill_count = 0;
                foreach( $kills as $kill )
                    if( $kill['primary_kill'] )
                        $kill_count++;   
            
                $hours_left = (int)$db->zombie_hours_to_starve( $game_info['id'], $player['uid'] );
                
                $b .= '<td class="table-cell-'.$on_off.'">'.$kill_count.'</td>'."\n";

                $b .= '<td class="table-cell-'.$on_off.'">'.$hours_left.'</td>'."\n";
            }
            elseif( $player['team'] == 'Deceased' )
            {
                $kills = $db->get_kills( $game_info['id'], $player['uid'] );
                $kill_count = 0;
                foreach( $kills as $kill )
                    if( $kill['primary_kill'] )
                        $kill_count++;
                
                $b .= '<td class="table-cell-'.$on_off.'">'.$kill_count.'</td>'."\n";
            }

            if( $on_off == 'on' )
                $on_off = 'off';
            else
                $on_off = 'on';
            
            $b .= "</tr>\n";
        }
        
        // display code
        
        $b .= "<hr><h3>Players</h3>\n";
        
        $num_humans = count( $humans );
        $num_zombies = count( $zombies );
        $num_deceased = count( $deceased );
        
        $b .= <<<ZZZ
<div class="status-area" style="min-width: 800px;">
<table>
<tr>
    <td class="form-label">Resistance:</td>
    <td class="form-value">$num_humans Humans</td>
    <td rowspan="4">
        <p><b>Last 6 Killed (Latest First)</b></p>
        <ol>
ZZZ;
        foreach( $latest_kills as $uid )
        {
            $user_info = $db->get_user_info( $uid );
            $b .= '<li><a href="player_details.php?gid='.$game_info['id'].'&uid='.$uid.'">'.$user_info['first_name'].' '.$user_info['last_name'].'</a></li>'."\n";
        }

        $b .= <<<ZZZ
        </ol>
        <p><b>Last 3 Starved (Latest First)</b></p>
        <ol>
ZZZ;
        foreach( $latest_starves as $uid )
        {
            $user_info = $db->get_user_info( $uid );
            $b .= '<li><a href="player_details.php?gid='.$game_info['id'].'&uid='.$uid.'">'.$user_info['first_name'].' '.$user_info['last_name'].'</a></li>'."\n";
        }

        $b .= <<<ZZZ
        </ol>
    </td>
</tr>
<tr>
    <td class="form-label">Horde:</td>
    <td class="form-value">$num_zombies Zombies</td>
</tr>
<tr>
    <td class="form-label">Deceased:</td>
    <td class="form-value">$num_deceased Starved</td>
</tr>
<tr>
    <td colspan="2">
ZZZ;

	  if( count($db->get_status_data($game_info['id'])) >= 2 )
	      $b .= '<img src="status_graph.php?dummy='.time().'&gid='.$game_info['id'].'" style="margin: 5px;" width="450" height="300" />';

    $b .= "</td></tr></table>\n";
        
	  $b .= "</div>\n";
        $b .= "<h4>Resistance (Humans)</h4>\n";
        if( count($humans) )
        {
            $b .= <<<ZZZ
<table style="border-collapse: collapse; width: 90%;">
<tr>
    <td class="table-header">Name</td>
    <td class="table-header">Survived (Hours)</td>
</tr>
ZZZ;
            $on_off = 'on';
            foreach( $humans as $player )
                display_player( $player );
            
            $b .= "</table>\n";
        }
        else
        {
            $b .= "<p>There are no humans in this game.</p>";
        }
        
        
        $b .= "<h4>Horde (Zombies)</h4>\n";
        if( count($zombies) )
        {
        $b .= <<<ZZZ
<table style="border-collapse: collapse; width: 90%;">
<tr>
    <td class="table-header">Name</td>
    <td class="table-header">Kills</td>
    <td class="table-header">Hours Until Starvation</td>
</tr>
ZZZ;
        $on_off = 'on';
        foreach( $zombies as $player )
            display_player( $player );
          
        $b .= "</table>\n";  
        }
        else
        {
            $b .= "<p>There are no zombies in this game.</p>";
        }
            
            
        $b .= "<h4>Deceased</h4>\n";
        if( count($deceased) )
        {
        $b .= <<<ZZZ
<table style="border-collapse: collapse; width: 90%;">
<tr>
    <td class="table-header">Name</td>
    <td class="table-header">Kills</td>
</tr>
ZZZ;
        $on_off = 'on';
        foreach( $deceased as $player )
            display_player( $player );
        
        $b .= "</table>\n";    
        }
        else
        {
            $b .= "<p>No zombies have died yet.</p>";
        }
        
    }
}

HVZ_make_page( $title, $b, 0 );

?>
