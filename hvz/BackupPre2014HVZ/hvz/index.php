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
<img src="./images/humanszsZombies.png" /><br />
<p>
Gamessoc and DCU Paintball Society present Humans vs Zombies in association with DCU Airsoft Society!


<img src="images/photos.png" />
<a href="http://www.redbrick.dcu.ie/~paintbal/"><img style="float: right; margin-top: -85px; height: 150px; width: 150px;" src="./images/paintball.jpg" /></a>
<a href="http://www.redbrick.dcu.ie/~airsoft/index.php"><img style="float: right; margin-top: -75px; margin-right: 35px" src="./images/airsoftlogo.png" /><br /></a>
<a href="http://games.dcu.ie"><img style="margin-left: 715px; margin-top: 20px" src="./images/gamessoc.png" /></a>
</p>
<p style="float: left; margin-top: -250px; width: 500px; margin-left: 30px;">Humans vs. Zombies (HvZ) is a game of moderated tag played at schools, camps, neighborhoods, military bases, and conventions across the world. Human players must remain vigilant and defend themselves with socks and dart blasters to avoid being tagged by a growing zombie horde. <br />
<a href="http://humansvszombies.org/about">Read more</a> or <a href="http://vimeo.com/1956330" >watch the documentary</a>. </p>




ZZZ;


HVZ_make_page( $title, $b, 0 );

?>
