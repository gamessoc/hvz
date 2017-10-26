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

$title = "Rules for Humans Vs. Zombies";

$b = '';

$b .= <<<ZZZ
<h4><img src="./images/overview.png"/></h4>
<p style = "width: 700px;">The game will be starting on Monday 23rd of October at midday (12pm). HvZ bands can be collected during Thursday's Raffle or at the stall in The Street (front of the Henry Grattan Building) on Thursday 19th, Friday 20th and Monday 23rd. The game will continue for the rest of the week with special missions throughout the week. The game time starts at 8am and ends at 8pm every day. Outside of these times, the game is inactive!</p><br />
<h4><img src="./images/objective.png"/></h4>
<ul>
	<li> The Zombies win when all human players have been tagged and turned into zombies.</li>
	<li> The Humans win by surviving long enough for all of the zombies to starve.</li>
</ul>
<h4><img src="./images/equipment.png" /></h4> 
<img src="./images/equipmentpic.png" style = " float:right;">
<p> This gear is required for all players: </p>
<ul>
	<li>Bandana. This is essential for playing the game-- if you have misplaced yours, contact a Gamessoc representative.</li>
	<li>One piece of paper with your kill PIN and your name on it. Please write clearly. If you do not have your PIN, it can be checked using the Control Panel feature of the HvZ website.</li>
</ul>
<p>Players are allowed the following weapons during the event.</p>
<ul>
	<li>Dart blasters. You can use any type of weapon that fires a foam projectile (dart, ball, disc) provided they <b>do not cause harm</b>.
	<li>Socks. These MUST be thrown. You cannot swing them or use anything to help swing them, and they must not have any heavy or sharp objects inside them.</li>
</ul>
<h4><img src="./images/safety.png" /></h4>
<p>These rules are designed for the safety of players, and for all DCU students. These are strictly enforced.<br>
Violation of safety rules will result in an immediate ban from the game.</p>
<ol>
<li>No realistic looking weaponry. Weapons must be brightly colored to avoid security issues.</li>
<li>Players are forbidden from entering construction sites on campus.</li>
<li>Players may not use cars or play where there is traffic.</li>
<li>Foam projectiles must be harmless, and not cause any pain. Modified weapons or projectiles are not allowed.</li>
<li>Players must ensure their own personal safety, and <b>under no circumstances put yourself in danger for the purposes of the game.</b></li>
</ol>
<h4><img src="./images/safezones.png" /></h4>
<a href="./images/Map2016.png"><img src="./images/Map2016.png" style= "float: right; height: 50%; width: 50%; padding-right: 40px;" /></a><br />
<p><b>No-Play Zones:</b> Humans cannot be tagged, and zombies cannot be stunned, by players within a No Play Zone. All Nerf weaponry should be concealed and headbands and armbands can be removed. No Play Zones are generally restricted access, privately owned, or would endanger players, and as such should not be entered while actively playing the game. If you are being engaged by a zombie, you must stun them or lose them before entering a No Play Zone unless absolutely necessary. Players who frequently use the No Play Zones in order to evade capture will be removed from the game.<br>
No-Play Zones include:</p>
		<ul>
		<li>Construction Sites</li>
		<li>Research Buildings</li>
		<li>Campus Residences</li>
		<li>The Library</li>
		<li>Lecture Halls and Bathrooms</li>
		<li>The Gym</li>
		<li>Any area a player must cross a road to access.</li>
		</ul>
<p><b>Safe Zone:</b> Within a Safe Zone, humans cannot be tagged and zombies cannot be stunned, and Nerf weaponry is generally allowed to be visible (this depends on the building). Armbands and headbands should be worn. Humans can attack from a Safe Zone to stun zombies outside, and zombies may infect a human inside the Safe Zone provided their two feet are in a Play Zone.<br>
Safe Zones include:</p>
<ul>
		<li>Academic Buildings</li>
		<li>Shops, Canteen, and NuBar</li>
		<li>The Mall</li>
		<li>The Helix</li>
		<li>Sports Hall</li>
		<li>The Marquee</li>
		<li>Outdoor Stairways (Spar)</li>
		</ul>
<p>All outside areas bound by the roads surrounding campus, excluding The Mall and the residence plazas, are considered Play Zones. Humans may stun zombies, and zombies may infect humans, freely in these areas. You <b>must</b> put your armband or headband back on before entering this area.</p>
<h4><img src="./images/human.png" /></h4>
<ul style="width: 800px;">
<li>Humans must wear a clearly visible orange armband at all times within Safe Zones and Play Zones. Masking involvement in the game by not wearing your band or wearing orange clothing can be grounds for removal from the game.</li>
<li>Humans can stun zombies for 15 minutes by tagging them with their weaponry. A tag is defined as a shot Nerf projectile or a thrown balled-up sock. Shooting a zombie's backpack also counts as a stun.</li>
<li>Humans who have been infected by a zombie must give their Kill PIN and name to the zombie. After an hour, a tagged Human joins the Zombies and wears their orange armband around their head.</li>
<li>If you have not been registered as killed an hour after you were caught, please inform the GamesSoc committee who will make necessary arrangements to fix this.</li>
<li>If a human has to leave campus for more than 24 hours or wishes to drop out of the game, please contact the GamesSoc committee to allow for appropriate changes to the player list.</li>

</ul>
<h4><img src="./images/zombie.png" /></h4>
<ul>
<li>Zombies must wear a clearly visible orange headband at all times within Safe Zones and Play Zones. Using hats, hoods, sunglasses worn on the head, or wearing the headband below long hair to mask involvement in the game is not allowed.
<li>Zombies must catch humans at least once every 24 hours, or they will starve and be eliminated from the game. Reporting a kill on the game page feeds a zombie for 24 hours, and a zombie also has the option to split kills with co-operating teammates. Zombies MUST enter a kill PIN into the site as quickly as possible, within an hour at the latest.
<li>An "infection" is a firm touch or grab to any part of a human's body. Grabbing a human's bag does not count as infection.
<li>When hit by a projectile, a Zombie is stunned for 15 minutes and cannot infect humans for that time. A stunned zombie may not shield other zombies from bullets, but they can chase after or run from other humans in order to lead them towards other zombies. Stunned zombies can still communicate with other members of their team.
<li>If a zombie is shot while already stunned, the stun timer is reset to 15 minutes.
<li>Zombies may not steal weapons or ammo from humans, they may not damage ammunition, and they may not use any "shields" to deflect projectiles.</li>
<br />
<p><b>Original Zombies:</b> At the start of the game, a number of Original Zombies are chosen to start the game. For the first hour of the game, or until they have killed five players, these zombies do not wear a headband. After these points, they wear a headband as usual.</p>

</ul>
<h4><img src="./images/other.png" /></h4>
<ul>
<li>Non players must not interfere with the game, in ways such as spying for a player. If a non-player offers to help, please refuse.</li>
<li>Keep your student ID on you at all times, don't argue with security if they have an issue with us running around with Nerf guns.</li>
<li>These rules are subject to change-- players will be notified on any further updates.</li>
<li><b>The honour system is key to HvZ</b>-- yes, you'll win if you cheat. But nobody's having fun then. Play fairly, or not at all.</li>
</ul>

ZZZ;


HVZ_make_page( $title, $b, 0 );

?>
