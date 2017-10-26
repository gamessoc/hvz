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

$title = "Control Panel";

$class_years = array( 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Alumni', 'Other' );

$b = '';

$uid = $_SESSION['uid'];

$pass_error = FALSE;
$pass_updated = FALSE;
if( isset($_POST['submit']) )
{
    $update_array = array();

    if( $_POST['new_pass'] )
    {
        if( $_POST['new_pass'] != $_POST['confirm_new_pass'] )
        {
            $pass_error = TRUE;
        }
        else
        {
            $pass_encrypted = md5( $_POST['new_pass'] );
            $update_array['passwd'] = $pass_encrypted;
            $pass_updated = TRUE;
        }
    }
    
    $update_array['email']      = $_POST['email'];
    $update_array['first_name'] = $_POST['fname'];
    $update_array['last_name']  = $_POST['lname'];
    $update_array['class_year'] = $_POST['class_year'];
    
    $cond_array = array();
    $cond_array['uid'] = $uid;
    
    $result = $db->update_tuple( 'users', $update_array, $cond_array );
    
    if( !$pass_updated && $result )
        $b .= '<p style="color: blue;">Preferences updated succesfully.</p>';
}

$user_info = $db->get_user_info( $uid );

// Games / PINs listing

$b .= <<<ZZZ
<form action="control_panel.php" method="POST">

<p><b>Entered Games</b></p>
<table style="border-collapse: collapse; width: 100%;">
<tr>
    <td class="table-header">Game</td>
    <td class="table-header">Status</td>
    <td class="table-header">Kill PIN</td>
</tr>
ZZZ;

$games_list = $db->get_games_list();

$user_pin_list = array();

foreach( $games_list as $gid )
{
    $players_list = $db->get_players_info( $gid );
    
    foreach( $players_list as $player )
        if( $player['uid'] == $uid && $player['team'] != 'Kicked' )
            $user_pin_list[$gid] = $player['kill_pin'];
}

$on_off = 'on';
foreach( $user_pin_list as $gid => $pin )
{
    $game_info = $db->get_game_info( $gid );
    $game_title = $game_info['title'];
    $game_status = $game_info['status'];
    $kill_pin = $user_pin_list[$gid];
   
    $b .= <<<ZZZ
<tr>
    <td class="table-cell-$on_off"><a href="view_game.php?id=$gid">$game_title</a></td>
    <td class="table-cell-$on_off">$game_status</td>
    <td class="table-cell-$on_off">$kill_pin</td>
</tr>
ZZZ;


    if( $on_off == 'on' )
        $on_off = 'off';
    else
        $on_off = 'on';
}

// Preferences

$b .= <<<ZZZ
</table>

<p><b>Preferences</b></p>
ZZZ;

if( $pass_error )
    $b .= '<p class="error">The passwords you typed did not match, maybe there was a typo. Please try again.</p>' . "\n";
    
if( $pass_updated )
    $b .= '<p style="color: blue;">Password updated succesfully.</p>' . "\n";

$b .= <<<ZZZ
<table>
<tr>
    <td class="form-label">New Password:</td>
    <td class="form-field"><input type="password" size="20" name="new_pass" /></td>
    <td class="form-extra">Leave blank to keep password the same</td>
</tr>
<tr>
    <td class="form-label">Confirm New Password:</td>
    <td class="form-field"><input type="password" size="20" name="confirm_new_pass" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="form-label">Class Year:</td>
    <td class="form-field">
        <select name="class_year">
ZZZ;

foreach( $class_years as $cy )
{
    if( $user_info['class_year'] == $cy )
        $b .= "<option value='$cy' selected='selected'>$cy</option>\n";
    else
        $b .= "<option value='$cy'>$cy</option>\n";
}

$b .= <<<ZZZ
        </select>
    </td>
    <td class="form-extra"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="form-label">E-Mail:</td>
    <td class="form-field"><input type="text" size="20" name="email" value="${user_info['email']}" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="form-label">First Name:</td>
    <td class="form-field"><input type="text" size="20" name="fname" value="${user_info['first_name']}" /></td>
    <td class="form-extra"></td>
</tr>
<tr>
    <td class="form-label">Last Name:</td>
    <td class="form-field"><input type="text" size="20" name="lname" value="${user_info['last_name']}" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Update Preferences" /></td><td></td></tr>
</table>
ZZZ;


HVZ_make_page( $title, $b, 1 );

?>
