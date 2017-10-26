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

$title = "User Login";

$login_error = FALSE;
if( isset($_POST['submit']) )
{
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    
    $user_info = $db->get_user_info_for_email( $email );
    
    if( !$user_info )
        $login_error = TRUE;
    elseif( $user_info['passwd'] == md5($pass) )
		    $_SESSION['uid'] = $user_info['uid'];
    else
	      $login_error = TRUE;
}
	
if( isset($_SESSION['uid']) ) 
{
    header("Location: index.php");
}


$b = <<<ZZZ
<p>You must <a href="register.php">Register</a> if you have not already before you can login here.</p>
<p>Can't remember your password? <a href="lost_password.php">Click Here</a>.</p>

<form method="POST" action="login.php">
<table>
ZZZ;

if( $login_error )
    $b .= '<tr><td class="error" colspan="2">Login information incorrect, have you registered?</td></tr>' . "\n";

$b .= <<<ZZZ
<tr>
    <td class="form-label">E-Mail:</td>
    <td class="form-field"><input type="text" size="20" name="email" /></td>
    
</tr>
<tr>
    <td class="form-label">Password:</td>
    <td class="form-field"><input type="password" size="20" name="pass" /></td>
</tr>
<tr><td class="form-submit" colspan="2"><input type="submit" name="submit" value="Login" /></td></tr>
</table>
ZZZ;

HVZ_make_page( $title, $b, 0 );

?>
