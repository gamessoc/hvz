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

$title = "Registration";

$class_years = array( 'First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Alumni', 'Post Graduate' );

$pass_error = FALSE;
$email_error = FALSE;
$dup_error = FALSE;
$required_error = FALSE;
$fname = '';
$lname = '';
$email = '';
$email_conf = '';
$class_year = '';
if( isset($_POST['submit']) )
{
    $email = $_POST['email'];
    $email_conf = $_POST['confirm_email'];
    $pass = $_POST['passwd'];
    $pass_conf = $_POST['confirm_passwd'];
    
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    
    $class_year = $_POST['class_year'];
    
    if( $pass != $pass_conf )
        $pass_error = TRUE;
    elseif( $email != $email_conf )
        $email_error = TRUE;
    elseif( $db->get_user_info_for_email($email) )
        $dup_error = TRUE;
    elseif( !$_POST['first_name'] || !$_POST['last_name'] || !$_POST['email'] || !$_POST['passwd'] )
        $required_error = TRUE;
    else
    {
        $vals_arr = array();
        
        $vals_arr['email'] = $email;
        $vals_arr['passwd'] = md5($pass);
        $vals_arr['first_name'] = $fname;
        $vals_arr['last_name'] = $lname;
        $vals_arr['class_year'] = $class_year;
        
        $db->insert_tuple( 'users', $vals_arr );
        
        header("Location: thanks.php");
    }
}

$b = <<<ZZZ
<p></p>
<form method="POST" action="register.php">
<table>
ZZZ;

if( $pass_error )
    $b .= '<tr><td class="error" colspan="3">Your password and confirmation were different, please type them again.</td></tr>' . "\n";
    
if( $email_error )
    $b .= '<tr><td class="error" colspan="3">Your e-mail address and confirmation were different, please type them again.</td></tr>' . "\n";
    
if( $dup_error )
    $b .= '<tr><td class="error" colspan="3">Your e-mail address is already registered, maybe you <a href="lost_password.php">lost your password</a>?</td></tr>' . "\n";
    
if( $required_error )
    $b .= '<tr><td class="error" colspan="3">You left something blank - All fields are required.</td></tr>' . "\n";    

$b .= <<<ZZZ
<tr>
    <td class="form-label">First Name:</td>
    <td class="form-field"><input type="text" size="20" name="first_name" value="$fname" /></td>
    <td class="form-extra">Type your name here as you wish others to see it</td>
</tr>
<tr>
    <td class="form-label">Last Name:</td>
    <td class="form-field"><input type="text" size="20" name="last_name" value="$lname" /></td>
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
    if( $class_year == $cy )
        $b .= "<option value='$cy' selected='selected'>$cy</option>\n";
    else
        $b .= "<option value='$cy'>$cy</option>\n";
}

$b .= <<<ZZZ
        </select>
    </td>
    <td class="form-extra">Class year, regardless of which college you're from</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="form-label">E-Mail:</td>
    <td class="form-field"><input type="text" size="20" name="email" value="$email" /></td>
    <td class="form-extra">Required to log in. Will not be visible to other users.</td>
</tr>
<tr>
    <td class="form-label">Confirm E-Mail:</td>
    <td class="form-field"><input type="text" size="20" name="confirm_email" value="$email_conf" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
    <td class="form-label">Password:</td>
    <td class="form-field"><input type="password" size="20" name="passwd" /></td>
    <td class="form-extra"></td>
</tr>
<tr>
    <td class="form-label">Confirm Password:</td>
    <td class="form-field"><input type="password" size="20" name="confirm_passwd" /></td>
    <td class="form-extra"></td>
</tr>
<tr><td></td><td class="form-submit"><input type="submit" name="submit" value="Register" /></td><td></td></tr>
</table>
<p> By registering, you agree to have understood and accept the <a href="./rules.php">Rules</a>.</p>
ZZZ;

HVZ_make_page( $title, $b, 0 );

?>
