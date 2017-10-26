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

$title = "Message An Admin";

$b = '';

$uid = $_SESSION['uid'];
$user_info = $db->get_user_info( $uid );

$subject = '';
$body = '';
$empty_subject = FALSE;
$empty_body = FALSE;
if( isset($_POST['submit']) )
{
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    
    $to = 'games@redbrick.dcu.com'; // Comma-delimited list of admins who can deal with complaints
    
    $name = $user_info['first_name'] . ' ' . $user_info['last_name'];
    
    if( !$subject )
        $empty_subject = TRUE;
    elseif( !$body )
        $empty_body = TRUE;
    else
    {
        $subject = 'HvZ Contact Form - ' . $subject;
    
        $message = <<<ZZZ
Automated Message from the HvZ Server. Message sent by user:
Name: $name
E-Mail: ${user_info['email']}
-----------------------------------------------------------------

ZZZ;
    
        $message .= wordwrap( $body, 70 );
        
        $result = mail( $to, $subject, $message, "From: ${user_info['email']}" );
        
        if( $result )
        {
            $b .= '<p style="color: blue;">Message sent succesfully.</p>';
            $subject = '';
            $body = '';
        }
        else
        {
            $b .= '<p class="error">Oops! There was an error, e-mail games@redbrick.dcu.ie if the problem persists.</p>';
        }
    }
}

$b .= <<<ZZZ
<p>Fill out the form below with a subject and message to be sent to an admin. Any
reply to your message will be sent to the e-mail address you signed up with when
you registered.</p>
ZZZ;

// Error reporting

if( $empty_subject )
    $b .= '<p class="error">A subject is required.</p>';
    
if( $empty_body )
    $b .= '<p class="error">A message body is required.</p>';

$b .= <<<ZZZ
<form action="contact_admin.php" method="POST">

<table>
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
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Send Message" /></td><td></td></tr>
</table>
</form>
ZZZ;


HVZ_make_page( $title, $b, 1 );

?>
