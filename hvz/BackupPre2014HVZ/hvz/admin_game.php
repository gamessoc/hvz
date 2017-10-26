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

$title = "Game Administration";

$b = '';

$game_info = FALSE;
if( isset($_GET['id']) )
{
    $db->check_zombie_starve( $_GET['id'] );
    $game_info = $db->get_game_info( $_GET['id'] );
    $players_info = $db->get_players_info( $_GET['id'] );
}

if( !$game_info )
{
    $b .= '<p class="error">Sorry, we could not find the game you were looking for.</p>' . "\n";
}
else
{
    if( isset($_POST['action']) )
    {
        $update_array = array();
        $cond_array = array();
        
        switch( $_POST['action'] )
        {
            case 'Update':
                $update_array['title']              = $_POST['game_title'];
                $update_array['zombie_starve_time'] = $_POST['starve_time'];
                $update_array['zombie_feed_time']   = $_POST['feed_time'];
                $update_array['zombie_stun_time']   = $_POST['stun_time'];
                $update_array['join_pass']          = $_POST['join_pass'];
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Open Registration':
                $update_array['registration'] = 'Open';
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Close Registration':
                $update_array['registration'] = 'Closed';
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Start Game':
                $update_array['status'] = 'In Progress';
                $update_array['started'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                // Update OZ start times to right now as the game starts
                foreach( $players_info as $player )
                {
                    if( $player['is_oz'] )
                    {
                        $update_array = array();
                        $update_array['infected'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
                        
                        $cond_array = array();
                        $cond_array['uid'] = $player['uid'];
                        
                        $db->update_tuple( 'games_users', $update_array, $cond_array );
                    }
                }     
                
                break;
                
            case 'End Game':
                $update_array['status'] = 'Concluded';
                $update_array['ended'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Select OZ':
                // Select and infest OZ
                $oz_pool_uids = array();
                foreach( $players_info as $player )
                {
                    if( $player['oz_pool'] )
                    {
                        array_push( $oz_pool_uids, $player['uid'] );
                    }
                }
                
                $k = array_rand( $oz_pool_uids );
                $oz_uid = $oz_pool_uids[$k];
                
                $result = $db->infect_player( $game_info['id'], $oz_uid, TRUE );
                
                if( !$result )
                {
                    $b .= '<p class="error">Infecting player failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                    break;
                }
                
                // Update OZ status for the game
                $update_array['oz_status'] = 'Hidden';
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Reveal OZ':
                $update_array['oz_status'] = 'Revealed';
                
                $cond_array['id'] = $game_info['id'];
                
                $result = $db->update_tuple( 'games', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Update failed, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }

                break;
                
            case 'Starve Zombies':
                $starve_amount = $_POST['starve_amount'];
                
                foreach( $players_info as $player )
                {
                    if( $player['team'] != 'Horde' )
                        continue;
                        
                    $update_array = array();
                    $update_array['feed_modifier'] = $player['feed_modifier'] - $starve_amount;
                    
                    $cond_array = array();
                    $cond_array['game_id'] = $_GET['id'];
                    $cond_array['uid'] = $player['uid'];
                    
                    $result = $db->update_tuple( 'games_users', $update_array, $cond_array );
                    
                    if( !$result )
                    {
                        $b .= '<p class="error">' . $db->last_error() . '</p>';
                    }
                }
                
                break;
                
            case 'Feed Zombies':
                $feed_amount = $_POST['feed_amount'];
                
                foreach( $players_info as $player )
                {
                    if( $player['team'] != 'Horde' )
                        continue;
                        
                    $update_array = array();
                    $update_array['feed_modifier'] = $player['feed_modifier'] + $feed_amount;
                    
                    $cond_array = array();
                    $cond_array['game_id'] = $_GET['id'];
                    $cond_array['uid'] = $player['uid'];
                    
                    $result = $db->update_tuple( 'games_users', $update_array, $cond_array );
                    
                    if( !$result )
                    {
                        $b .= '<p class="error">' . $db->last_error() . '</p>';
                    }
                }
                
                break;
                
            case 'Infect Humans':
                $humans_array = array();
                
                foreach( $players_info as $player )
                    if( $player['team'] == 'Resistance' )
                        array_push( $humans_array, $player['uid'] );
                        
                if( !$humans_array )
                    break;
                
                $convert = $_POST['conversion_amount'];
                
                if( count($humans_array) < $convert )
                    $convert = count($humans_array);
                    
                $chosen_keys = array_rand($humans_array, $convert);
                
                if( $convert == 1 )
                    $chosen_keys = array( $chosen_keys ); // array_rand() returns a key instead of an array when $convert is 1
                
                foreach( $chosen_keys as $k )
                {
                    $result = $db->infect_player( $_GET['id'], $humans_array[$k], $player['is_oz'] );
                    if( !$result )
                        $b .= '<p class="error">' . $db->last_error() . '</p>';
                    else
                        $db->mail_status_change( $_GET['id'], $humans_array[$k], 'Infected' );
                }
                
                break;
                
            case 'Cure Zombies':
                $zombies_array = array();
                
                foreach( $players_info as $player )
                    if( $player['team'] == 'Horde' )
                        array_push( $zombies_array, $player['uid'] );
                        
                if( !$zombies_array )
                    break;
                
                $convert = $_POST['conversion_amount'];
                
                if( count($zombies_array) < $convert )
                    $convert = count($zombies_array);
                    
                $chosen_keys = array_rand($zombies_array, $convert);
                
                if( $convert == 1 )
                    $chosen_keys = array( $chosen_keys ); // array_rand() returns a key instead of an array when $convert is 1
                
                foreach( $chosen_keys as $k )
                {
                    $result = $db->cure_player( $_GET['id'], $zombies_array[$k] );
                    if( !$result )
                        $b .= '<p class="error">' . $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Kick':
                $update_array = array();
                $update_array['team'] = 'Kicked';
            
                $cond_array = array();
                $cond_array['uid'] = $_POST['kicked_player'];
                
                $result = $db->update_tuple( 'games_users', $update_array, $cond_array );
                
                if( !$result )
                {
                    $b .= '<p class="error">Failed to kick player, SQL returned an error<br />';
                    $b .= $db->last_error() . '</p>';
                }
                
                break;
                
            case 'Mass E-Mail':
                header( "Location: mass_email.php?gid=${game_info['id']}" );
        }
        
        // A little wasteful to query this out again, but safer for proper page update
        $db->check_zombie_starve( $_GET['id'] );
        $game_info = $db->get_game_info( $_GET['id'] );
        $players_info = $db->get_players_info( $_GET['id'] );
    }


    if( $game_info['registration'] == 'Closed' )
        $registrationToggle = 'Open Registration';
    else
        $registrationToggle = 'Close Registration';
      
    if( $game_info['oz_status'] == 'Unchosen' )
    {
        $next_oz = 'Select OZ';
        $oz_confirm_msg = 'Are you sure you want to select the original zombie (picked at random)?';
    }
    else
    {
        $next_oz = 'Reveal OZ';
        $oz_confirm_msg = 'Are you sure you want to reveal the identity of the original zombie to all players?';
    }
    
    if( $game_info['status'] == 'Staging' )
    {
        $next_status = 'Start Game';
        $status_confirm_msg = 'Are you sure you want to start the game? Have you chosen an original zombie? There is no going back!';
    }
    else
    {
        $next_status = 'End Game';
        $status_confirm_msg = 'Is the game really over? All the humans are zombies or all the zombies have starved?';
    }

    $b .= <<<ZZZ
<form method="POST" action="admin_game.php?id=${game_info['id']}" onsubmit="return checkSubmit(this);">

<input type="hidden" name="kicked_player" value="" /> <!-- To be used by JS when a player is kicked -->
<input type="hidden" name="submit_ok" value="1" />
<input type="hidden" name="starve_amount" value="0" />
<input type="hidden" name="feed_amount" value="0" />
<input type="hidden" name="conversion_amount" value="0" />

<div class="status-area">
<table>
<tr>
    <td class="form-label">Game:</td>
    <td class="form-value"><input type="text" name="game_title" size="30" value="${game_info['title']}" /></td>
    <td class="form-control"><input name="action" type="submit" value="Update" /></td>
</tr>
<tr>
    <td class="form-label">Status:</td>
    <td class="form-value">${game_info['status']}</td>
    <td class="form-control"><input name="action" type="submit" value="$next_status" onclick="setFormValue( this, 'submit_ok', confirm('$status_confirm_msg') );" /></td>
</tr>
<tr>
    <td class="form-label">Registration:</td>
    <td class="form-value">${game_info['registration']}</td>
    <td class="form-control"><input name="action" type="submit" value="$registrationToggle" onclick="setFormValue( this, 'submit_ok', confirm('Are you sure you want to $registrationToggle?') );" /></td>
</tr>
<tr>
    <td class="form-label">Zombies Starve After:</td>
    <td class="form-value"><input type="text" name="starve_time" size="3" value="${game_info['zombie_starve_time']}" /> Hours</td>
    <td class="form-control"><input name="action" type="submit" value="Update" /></td>
</tr>
<tr>
    <td class="form-label">Zombies Are Fed From A Kill By:</td>
    <td class="form-value"><input type="text" name="feed_time" size="3" value="${game_info['zombie_feed_time']}" /> Hours</td>
    <td class="form-control"><input name="action" type="submit" value="Update" /></td>
</tr>
<tr>
    <td class="form-label">Zombies Are Stunned For:</td>
    <td class="form-value"><input type="text" name="stun_time" size="3" value="${game_info['zombie_stun_time']}" /> Minutes</td>
    <td class="form-control"><input name="action" type="submit" value="Update" /></td>
</tr>
<tr>
    <td class="form-label">Original Zombie(s):</td>
    <td class="form-value">${game_info['oz_status']}</td>
    <td class="form-control"><input name="action" type="submit" value="$next_oz" onclick="setFormValue( this, 'submit_ok', confirm('$oz_confirm_msg') );" /></td>
</tr>
<tr>
    <td class="form-label">Join Password:</td>
    <td class="form-value"><input type="text" name="join_pass" size="20" value="${game_info['join_pass']}" /></td>
    <td class="form-control"><input name="action" type="submit" value="Update" /></td>
</tr>
</table>
</div>

<hr>
<h3>Admin Controls</h3>

<p>
<input name="action" type="submit" value="Starve Zombies" onclick="var conf = setFormValue(this, 'starve_amount', prompt('Enter a number of hours (4,6,etc.) to starve all zombies by:') ); setFormValue(this, 'submit_ok', conf);" /><br />
Click to enter a number of hours to starve all zombies by (Hours Until Starvation<br />
is reduced by this amount).
</p>

<p>
<input name="action" type="submit" value="Feed Zombies" onclick="var conf = setFormValue( this, 'feed_amount', prompt('Enter a number of hours (4,6,etc.) to feed all zombies by, or enter \'FULL\' to feed all zombies to their initial starvation time:') ); setFormValue(this, 'submit_ok', conf);" /><br />
Click to enter a number of hours to feed all zombies by (Hours Until Starvation<br />
is increased by this amount) up to a maximum of the intial starvation time.<br />
Enter FULL to feed them all to max.
</p>

<p>
<input name="action" type="submit" value="Infect Humans" onclick="var conf = setFormValue( this, 'conversion_amount', prompt('Enter a number of humans (chosen at random) to be turned into zombies (NO UNDO):') ); setFormValue(this, 'submit_ok', conf);" /><br />
Click to enter a number of human players to be converted into zombies. They<br />
will be chosen at random.
</p>

<p>
<input name="action" type="submit" value="Cure Zombies" onclick="var conf = setFormValue( this, 'conversion_amount', prompt('Enter a number of zombies (chosen at random) to be turned into humans (NO UNDO):') ); setFormValue(this, 'submit_ok', conf);" /><br />
Click to enter a number of zombies to be converted into humans. They<br />
will be chosen at random.
</p>

<p>
<input name="action" type="submit" value="Mass E-Mail" /><br />
Send a mass e-mail message to all players, just humans, or just zombies.
</p>


<hr>
<h3>Players</h3>

ZZZ;

    if( !$players_info )
    {
        $b .= "<p>No players have currently joined this game.</p>\n";
    }
    else
    {
        $b .= <<<ZZZ
<table style="border-collapse: collapse; width: 100%;">
<tr>
    <td class="table-header">Name</td>
    <td class="table-header">Team</td>
    <td class="table-header">Kills</td>
    <td class="table-header">Hours Until Starvation</td>
    <td class="table-header">E-Mail</td>
    <td class="table-header">Kill PIN</td>
    <td class="table-header">Kick</td>
</tr>
ZZZ;
       
        $on_off = 'on';
        foreach( $players_info as $player )
        {
            $b .= "<tr>\n";
            
            $user_info = $db->get_user_info( $player['uid'] );
            
            $b .= '<td class="table-cell-'.$on_off.'"><a href="player_details.php?gid='.$game_info['id'].'&uid='.$user_info['uid'].'">'.$user_info['first_name'].' '.$user_info['last_name'].'</a></td>'."\n";
            
            if( $player['team'] == 'Horde' && $player['is_oz'] )
                $team_display = 'Horde (Original)';
            else
                $team_display = $player['team'];
            
            $b .= '<td class="table-cell-'.$on_off.'">'.$team_display.'</td>'."\n";
            
            if( $player['team'] != 'Horde' )
                $hours_left = 'N/A';
            else
                $hours_left = (int)$db->zombie_hours_to_starve( $game_info['id'], $player['uid'] );
                
            $kills = $db->get_kills( $game_info['id'], $player['uid'] );
            $kill_count = 0;
            foreach( $kills as $kill )
                if( $kill['primary_kill'] )
                    $kill_count++;
            
            $b .= '<td class="table-cell-'.$on_off.'">'.$kill_count.'</td>'."\n";
            
            $b .= '<td class="table-cell-'.$on_off.'">'.$hours_left.'</td>'."\n";
            
            $b .= '<td class="table-cell-'.$on_off.'">'.$user_info['email'].'</td>'."\n";
            
            $b .= '<td class="table-cell-'.$on_off.'">'.$player['kill_pin'].'</td>'."\n";
            
            $b .= '<td class="table-cell-'.$on_off.'"><input name="action" type="submit" value="Kick" onclick="setFormValue( this, \'kicked_player\', \''.$user_info['uid'].'\' ); setFormValue( this, \'submit_ok\', confirm( \'Are you sure you want to kick '.$user_info['first_name'].' '.$user_info['last_name'].' from this game (Permanent)?\') );" /></td>'."\n";
            
            if( $on_off == 'on' )
                $on_off = 'off';
            else
                $on_off = 'on';
            
            $b .= "</tr>\n";
            
        }
        
        $b .= "</table>\n";
    }
}

$b .= "</form>\n";

HVZ_make_page( $title, $b, 2 );

?>
