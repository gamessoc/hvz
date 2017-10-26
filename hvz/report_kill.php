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

$title = "Report A Kill";

$b = '';

$uid = $_SESSION['uid'];

if( isset($_GET['gid']) )
    $gid = $_GET['gid'];
else
{
    $a = '<p class="error">Sorry, an error has occured and this page cannot display. Please contact an admin if you clicked a link to get this page.</p>';
    HVZ_make_page( $title, $a, 1 );
    exit();
}

$db->check_zombie_starve( $gid );
$game_info = $db->get_game_info( $gid );
$user_info = $db->get_user_info( $uid );
$players_info = $db->get_players_info( $gid );

$selected_human = -1;
$selected_shared = array();
$pin = '';

$player_is_not_zombie = FALSE;
$killed_is_not_resistance = FALSE;
$no_kill_selected = FALSE;
$wrong_pin = FALSE;
$dup_share = FALSE;
$db_error = FALSE;
if( isset($_POST['submit']) )
{

    //$selected_human = $_POST['killed_player'];
    $pin = $_POST['kill_pin'];
    
    $post_shares = array( $_POST['share_kill_1'], $_POST['share_kill_2'], $_POST['share_kill_3'], $_POST['share_kill_4'], $_POST['share_kill_5'] );

    $shared_players = array();

    $killer_info = FALSE;
    $killed_info = FALSE;
    foreach( $players_info as $player )
    {
        if( $player['uid'] == $uid )
        {
            $killer_info = $player;
        
            if( $player['team'] != 'Horde' )
                $player_is_not_zombie = TRUE;
        }
        elseif( $player['kill_pin'] == $_POST['kill_pin'] )
        {
            $killed_info = $player;
            
            if( $player['team'] != 'Resistance' )
                $killed_is_not_resistance = TRUE;
        }
        elseif( in_array($player['uid'], $post_shares) )
        {
            if( !array_key_exists($player['uid'], $shared_players) )
            {
                $shared_players[$player['uid']] = $player;
                array_push( $selected_shared, $player['uid'] );
            }
            
            if( count(array_keys($post_shares, $player['uid'])) > 1 ) // if this player's uid has more than one key in the post_shares array, they were entered more than once
            {
                $dup_share = TRUE;
            }
        }
    }
    
    if( !$killer_info )
        $player_is_not_zombie = TRUE;
        
    if( !$killed_info )
        $no_kill_selected = TRUE;
        
    if( $killed_info['kill_pin'] != $_POST['kill_pin'] )
        $wrong_pin = TRUE;
        
    if( $killer_info && $killed_info && !$wrong_pin && !$dup_share && !$player_is_not_zombie && !$killed_is_not_resistance )
    {
        $db->infect_player( $gid, $killed_info['uid'] );
    
        $insert_array = array();
        $insert_array['gid']       = $gid;
        $insert_array['killer_id'] = $killer_info['uid'];
        $insert_array['killed_id'] = $killed_info['uid'];
        $insert_array['kill_time'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
        
        $index = 1;
        foreach( $shared_players as $share )
        {
            $insert_array["share${index}_id"] = $share['uid'];
            $index++;
        }
        
        $db->mail_status_change( $gid, $killed_info['uid'], 'Killed' );
        
        $result = $db->insert_tuple( 'kills', $insert_array );
        
        if( !$result )
            $db_error = TRUE;
        
		}
}

$humans = array();
$zombies = array();
foreach( $players_info as $player )
{
    if( $player['team'] == 'Resistance' || ($player['team'] == 'Horde' && $player['is_oz'] && $game_info['oz_status'] == 'Hidden') )
        $humans[$player['uid']] = $player;
    elseif( $player['team'] == 'Horde' && !($player['is_oz'] && $game_info['oz_status'] == 'Hidden') )
        $zombies[$player['uid']] = $player;
}


$b .= <<<ZZZ
<p>Below you may report a kill (obviously you must be a zombie). Select the name
of the human you killed from the drop-down list, and enter the PIN from the index
card or piece of paper that the player gave you when they were tagged. Without
this PIN you cannot report them killed, so if you do not have it stop now and
track down the player you killed to get their PIN. You may not report a kill
made by another player, they must login with their account and report it themselves.</p>

<p>You may share your kill with up to 5 other players. Each kill is currently worth
<b>${game_info['zombie_feed_time']}</b> hours of feeding time. If you share
your kill, the feed time is <b>divided evenly among the players</b> (you get less feed time); however, if your kill
would put you over the initial starvation period for zombies (currently
<b>${game_info['zombie_starve_time']}</b> hours), you will not receive credit
for the excess time. So, if you already are "well fed", you may wish to consider
sharing with other zombies that helped you make your kill.</p>

<p><b>You may not share a kill</b> with a zombie who was not nearby when you made the kill,
unless they somehow helped you make the kill (setup the killed player, called you
about where they were, etc.). This is on the honor system, but violating this
rule is grounds for ejection from the game.</p>
ZZZ;

// Error reporting

if( $player_is_not_zombie )
    $b .= '<h4 class="error" >Sorry, you do not appear to be a member of the Horde team of this game.</p>';
    
if( $killed_is_not_resistance )
    $b .= '<h4 class="error">Sorry, the player you selected is not a member of the resistance. If you are attempting to modify a previously reported kill, <a href="contact_admin.php">Contact An Admin</a> instead.</p>';
    
if( $no_kill_selected )
    $b .= '<h4 class="error">You must select which human player you killed.</p>';
    
if( $wrong_pin )
    $b .= '<h4 class="error">You did not enter the correct PIN for the player you selected as killed.</p>';
    
if( $dup_share )
    $b .= '<h4 class="error">Sorry, you cannot share a kill with a player more than once, duplicates have been removed.</p>';
    
if( $db_error )
    $b .= '<h4 class="error">Sorry, an internal database error has occured. Please contact an admin and report this problem.</p>';

$b .= <<<ZZZ
<form action="report_kill.php?gid=$gid" method="POST">

<table>
<tr>

ZZZ;

foreach( $humans as $player )
{
    if( $user_info['uid'] == $player['uid'] )
        continue; // skip self
        
    $player_user_info = $db->get_user_info( $player['uid'] );
    $b .= "<option value='${player['uid']}'";
    
    if( $selected_human == $player['uid'] )
        $b .= ' selected="selected"';
    
    $b .= ">${player_user_info['first_name']} ${player_user_info['last_name']}</option>";
}
    
$b .= <<<ZZZ

<tr>
    <td class="form-label">Kill PIN:</td>
    <td class="form-field"><input type="text" size="8" name="kill_pin" value="$pin" /></td>
    <td class="form-extra">PIN of the player you killed</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
ZZZ;

for( $i = 1; $i <= 5; $i++ )
{
    $b .= <<<ZZZ
<tr>
    <td class="form-label">Share Kill With:</td>
    <td class="form-field">
        <select name="share_kill_$i">
            <option value="-1">Select A Zombie To Share With</option>
ZZZ;
    
    $selected_set = FALSE;
    foreach( $zombies as $player )
    {
        if( $user_info['uid'] == $player['uid'] )
            continue; // skip self
    
        $player_user_info = $db->get_user_info( $player['uid'] );
        $b .= "<option value='${player['uid']}'";

        $key = array_search( $player['uid'], $selected_shared );
        if( $key !== FALSE && !$selected_set ) // type-sensitive not equal, key 0 evaluates false
        {
            unset( $selected_shared[$key] );
            $selected_set = TRUE;
            $b .= ' selected="selected"';
        }
        
        $b .= ">${player_user_info['first_name']} ${player_user_info['last_name']}</option>";
    }
    
    $b .= <<<ZZZ
        </select>
    <td class="form-extra">Optional</td>
</tr>
ZZZ;
}

$b .= <<<ZZZ
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Report Kill" /></td><td></td></tr>
</table>
</form>
ZZZ;


HVZ_make_page( $title, $b, 1 );

?>
