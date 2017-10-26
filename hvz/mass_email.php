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

$title = "Mass E-Mail";

$b = '';

$gid_ok = FALSE;
if( isset($_GET['gid']) )
{
    $gid = $_GET['gid'];
    $game_info = $db->get_game_info( $gid );
    if( $game_info )
        $gid_ok = TRUE;
}

if( !$gid_ok )
{
    $a = '<p class="error">Sorry, an error has occured and this page cannot display. Please contact an admin if you clicked a link to get this page.</p>';
    HVZ_make_page( $title, $a, 1 );
    exit();
}

$players_info = $db->get_players_info( $gid );

$humans = array();
$zombies = array();
foreach( $players_info as $player )
{
    if( $player['team'] == 'Resistance' )
        $humans[$player['uid']] = $player;
    elseif( $player['team'] == 'Horde' )
        $zombies[$player['uid']] = $player;
}

$subject = 'DCU Gamessoc HvZ - ';
$body = '';

$empty_subject = FALSE;
$empty_body = FALSE;
if( isset($_POST['submit']) )
{
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    
    if( !$subject )
        $empty_subject = TRUE;
    elseif( !$body )
        $empty_body = TRUE;
    else
    {
        $to_array = array();
        switch( $_POST['group'] )
        {
            case 'all':
                foreach( $players_info as $recipient )
                {
                    $user_info = $db->get_user_info( $recipient['uid'] );
                    array_push( $to_array, $user_info['email'] );
                }
                break;
                
            case 'resistance':
                foreach( $humans as $recipient )
                {
                    $user_info = $db->get_user_info( $recipient['uid'] );
                    array_push( $to_array, $user_info['email'] );
                }
                break;
                
            case 'horde':
                foreach( $zombies as $recipient )
                {
                    $user_info = $db->get_user_info( $recipient['uid'] );
                    array_push( $to_array, $user_info['email'] );
                }
                break;
        }
        
        $message = wordwrap( $body, 70 );
        
        $message .= "\n\n";
        $message .= "------------------------------------------------------------\n";
        $message .= "This is a mass e-mail sent by the DCU Gamessoc Humans vs. Zombies website.\n";
        $message .= "If you are not playing, and feel this is spam, report abuse to games@redbrick.dcu.ie.\n";
        $message .= "\r\n";
        $message .= "DO NOT REPLY TO THIS E-MAIL. If you need to contact an admin, please\n";
        $message .= "e-mail us at games@redbrick.dcu.ie.\n";
        
        $bad_result = FALSE;
        
        foreach( $to_array as $to )
        {
            $result = mail( $to, $subject, $message, "From: noreply@redbrick.dcu.ie" ); // INSERT YOUR WEB SERVER INSTEAD OF example.com (or EVERY spam filter in the world will bounce you)
            if( !$result )
                $bad_result = TRUE;
        }
        
        if( !$bad_result )
            header( "Location: admin_game.php?id=$gid" );
        else
            $b .= '<p class="error">Oops! There was an error, the mailer daemon would not accept the e-mail.</p>';
    }
}

$b .= <<<ZZZ
<p>Fill out the form below with a subject and message, and select which group of
players to e-mail. The from line of the e-mail will be "noreply@redbrick.dcu.ie",
this is necessary to avoid getting filtered as spam. Users will not be able
to reply directly, instead the e-mail will include a standard footer stating
that this is a mass e-mail from this site and listing admin contact information.</p>

<p>It may take several minutes for the mail to show up in your inbox (assuming you are
part of the group being e-mailed).</p>
ZZZ;

// Error reporting

if( $empty_subject )
    $b .= '<p class="error">A subject is required.</p>';
    
if( $empty_body )
    $b .= '<p class="error">A message body is required.</p>';

$b .= <<<ZZZ
<form action="mass_email.php?gid=$gid" method="POST">

<table>
<tr>
    <td class="form-label">Game:</td>
    <td class="form-field"><b>${game_info['title']}</b></td>
    <td class="form-extra"></td>
</tr>
<tr>
    <td class="form-label">To Group:</td>
    <td class="form-field">
        <select name="group">
            <option value="all">All Players</option>
            <option value="resistance">Resistance</option>
            <option value="horde">Horde</option>
        </select>
    </td>
    <td class="form-extra"></td>
</tr>
<tr>
    <td class="form-label">Subject:</td>
    <td class="form-field"><input type="text" name="subject" size="30" value="$subject" /></td>
    <td class="form-extra"></td>
</tr>
<tr>
    <td class="form-label">Message Body:</td>
    <td class="form-field"><textarea name="body" cols="70" rows="8">$body</textarea></td>
    <td class="form-extra"></td>
</tr>
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Send E-Mail" /></td><td></td></tr>
</table>
</form>
ZZZ;


HVZ_make_page( $title, $b, 2 );

?>
