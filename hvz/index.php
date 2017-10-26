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

$title = "Home";

$b = '';

if( isset($_SESSION['uid']) )
{
    $uid = $_SESSION['uid'];
    $user_info = $db->get_user_info( $uid );
    
    $b .= '<h4>Welcome back ' . $user_info['first_name'] . '!</h4>';
}

$b .= <<<ZZZ
<div class="width">
<img style="margin-left: 160px" src="./images/hvzlogo.png" width="600" />
</div><br />
<p style="text-align:center;">DCU Games Society presents Humans V Zombies!</p>


<img style="margin-left: 100px; margin-top: 20px; padding-bottom: 500px" src="./images/photos.png" /></p>
<h3 style="float: left; margin-top: -480px; width: 500px; margin-left: 30px;">What is Humans V Zombies?</h3>
<p style="float: left; margin-top: -450px; width: 500px; margin-left: 30px;">Humans V Zombies (HvZ) is a specialised game of tag played for one week in October annually on DCU's Glasnevin Campus. Human players must remain vigilant, and defend themselves from Zombies by using dart blasters, balled-up socks, or by running! Failure to evade the zombies will result in you joining the zombie horde. As a Zombie, your goal is to eat Humans and stay alive! Alternatively, you could go to the NuBar, have a nice cold pint, and wait for all this to blow over.</p>
ZZZ;



HVZ_make_page( $title, $b, 0 );

?>
