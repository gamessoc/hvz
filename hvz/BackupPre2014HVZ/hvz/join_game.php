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

$title = "Join Game";

$b = '';

$game_info = FALSE;
if( isset($_GET['id']) )
{
    $game_info = $db->get_game_info( $_GET['id'] );
    $players_info = $db->get_players_info( $_GET['id'] );
    
    $uid = $_SESSION['uid'];
}

$user_in_game = FALSE;
foreach( $players_info as $info_array )
    if( $info_array['uid'] == $uid )
        $user_in_game = TRUE;

if( !$game_info )
{
    $b .= '<p class="error">Sorry, we could not find the game you were looking for.</p>' . "\n";
}
elseif( $user_in_game )
{
    $b .= '<p class="error">You have already joined this game!</p>' . "\n";
}
else
{
    $b .= "<p><b>Joining Game: ${game_info['title']}</b></p>\n";
    
    $b .= <<<ZZZ
<p>This will enter you into the game as a participant. You will join as a human
player, unless you check the OZ Pool option. If you do, you will join the pool
of players from whom the Original Zombie is chosen. If you do not check this option,
you are guaranteed to start as a human.</p>
ZZZ;
    
    if( $game_info['join_pass'] )
        $b .= <<<ZZZ
<p>This game requires a join password in order to join the game. You need to
get this join password from an admin if you do not have it. To get the password,
you must attend an information session or fulfill some other requirement. Check
the home page for an announcement for more information, or contact an admin.
ZZZ;

    $join_pass_error = FALSE;
    $oz_pool = FALSE;
    $oz_pool_sql = '0';
    if( isset($_POST['submit']) )
    {
        if( isset($_POST['oz_pool']) )
        {
            $oz_pool = TRUE;
            $oz_pool_sql = '1';
        }
            
        if( $game_info['join_pass'] && $_POST['join_pass'] != $game_info['join_pass'] )
            $join_pass_error = TRUE;
        else
        {
            $vals_arr = array();
            
            $vals_arr['game_id'] = $game_info['id'];
            $vals_arr['uid'] = $uid;
            $vals_arr['oz_pool'] = $oz_pool_sql;
            $vals_arr['joined'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
            $vals_arr['kill_pin'] = rand( 1000, 9999 );
            
            $db->insert_tuple( 'games_users', $vals_arr );
            
            $db->mail_status_change( $game_info['id'], $uid, 'Joined' );
            
            header( "Location: joined.php?gid=${game_info['id']}&pin=${vals_arr['kill_pin']}" );
        }
    }

    $b .= <<<ZZZ
    <p></p>

    <form method="POST" action="join_game.php?id=${game_info['id']}">
    <table>
ZZZ;

    if( $join_pass_error )
        $b .= '<tr><td class="error" colspan="3">You did not enter the correct join password, please try again.</td></tr>' . "\n";  

    if( $game_info['join_pass'] )
    {
        $b .= <<<ZZZ
    <tr>
        <td class="form-label">Join Password:</td>
        <td class="form-field"><input type="text" size="15" name="join_pass" /></td>
        <td class="form-extra">Password received from admin</td>
    </tr>
ZZZ;
    }
    
    $b .= '<tr>';
    $b .= '<td class="form-label">OZ Pool:</td>';
    
    $b .= '<td class="form-field"><input type="checkbox" name="oz_pool" ';
    if( $oz_pool )
        $b .= 'checked="checked" ';
    $b .= '/></td>';
    
    $b .= <<<ZZZ
        <td class="form-extra"></td>
    </tr>
    <tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Join Game" /></td><td></td></tr>
    </table>
    </form>
ZZZ;
}

HVZ_make_page( $title, $b, 1 );

?>
