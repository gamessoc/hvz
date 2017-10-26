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

$title = "Lost Password";

$b = '';

$bad_email = FALSE;
if( isset($_POST['submit']) )
{
    $user_info = $db->get_user_info_for_email( $_POST['email'] );
    
    if( !$user_info )
        $bad_email = TRUE;
    else
    {
        $new_pass = rand( 111111, 999999 );
        
        $update_array = array();
        $update_array['passwd'] = md5( $new_pass );
        
        $cond_array = array();
        $cond_array['email'] = $user_info['email'];
        
        $result = $db->update_tuple( 'users', $update_array, $cond_array );
        
        if( $result )
        {
            $to = $_POST['email'];
            $subject = 'Humans Vs. Zombies - Password Reset';
            
            $message = <<<ZZZ
We have received a password reset request for an account with your
e-mail address. If you did not request a password reset, either 
ignore this e-mail or report abuse to games@redbrick.dcu.ie

Your new password is:
$new_pass

You can enter this password, along with your e-mail address, at http://www.games.dcu.ie/hvz/login.php
To login to the web site. You may wish to visit the control panel
and change your password to something else more secure.

DO NOT REPLY TO THIS E-MAIL, any problems or concerns should be 
addressed to games@redbrick.dcu.ie.
ZZZ;

            $result = mail( $to, $subject, $message, "From: noreply@example.com" ); // CHANGE example.com TO YOUR WEB SERVER
            
            if( $result )
                $b .= '<p style="color: blue;">Password reset e-mail sent succesfully.</p>';
            else
                $b .= '<p class="error">There was an error processing your request, if the problem persists please report it to --- INSERT ADMIN E-MAIL HERE ---.</p>';
        }
        else
            $b .= '<p class="error">There was an error processing your request, if the problem persists please report it to --- INSERT ADMIN E-MAIL HERE ---.</p>';
    }
}

$b .= <<<ZZZ
<p>Enter your e-mail below, and we will reset your password and e-mail it to
you. It may take several minutes for the reset e-mail to arrive in your inbox
once it is sent.</p>
ZZZ;

// Error reporting

if( $bad_email )
    $b .= '<p class="error">Sorry, we cannot find that e-mail address in our database.</p>';

$b .= <<<ZZZ
<form action="lost_password.php" method="POST">

<table>
<tr>
    <td class="form-label">E-Mail:</td>
    <td class="form-field"><input type="text" size="20" name="email" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Reset Password" /></td><td></td></tr>
</table>
</form>
ZZZ;

HVZ_make_page( $title, $b, 0 );

?>
