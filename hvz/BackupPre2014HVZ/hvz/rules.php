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
<p style = "width: 700px;">The game will be starting on Monday 10th of November. The rules are listed below. You will be notified of any changes to these rules via email. We'll be in college to hand out bandannas to the players and start the event. The game will continue for the rest of the week with special missions throughout the week. The game time starts at 9am and ends at 8pm every day. This is so people can head home and rest at the end of the day.</p><br />
<h4><img src="./images/objective.png"/></h4>
<ul>
	<li> The Zombies win when all human players have been tagged and turned into zombies.</li>
	<li> The Humans win by surviving long enough for all of the zombies to starve.</li>
</ul>
<h4><img src="./images/equipment.png" /></h4> 
<img src="./images/equipmentpic.png" style = " float:right;">
<p> This gear is required for all players: </p>
<ul>
	<li>Bandana</li>
	<li>Nerf guns. You can use any type of nerf weapon as long as they do not hurt.</li>
	<li>Socks. These MUST be thrown. You cannot swing them or use anything to help swing them.</li>
	<li>One 3x5 index card</li>
</ul>
<h4><img src="./images/safety.png" /></h4>
<p> Rules created for the safety of all players are strictly enforced. Violation of safety rules will result in a ban from the game.</p>
<ol>
<li>No realistic looking weaponry. Weapons must be brightly colored</li>
<li>Blasters may not be visible inside of academic buildings or jobs on campus.</li>
<li>Players may not use cars or play where there is traffic.</li>
<li> Foam darts must be harmless, and not cause any pain</li>
</ol>
<h4><img src="./images/safezones.png" /></h4>
<img src="./images/map3.png" style = "float: right; height: 70%; width: 70%;"   /><br />
Some Areas on Campus are "no play zones" where the game is permanently suspended. 
Dart blasters must be concealed and no players may be stunned or tagged. These areas include:
		<ul>
		<li>Academic buildings</li>
		<li>The Library</li>
		<li>Bathrooms</li>
		<li>The Gym</li>
		</ul>
<p>Some areas are merely 'safe zones', gameplay continues, but humans can not be tagged while they are inside the zone, unless a zombie can reach them with two feet outside of the zone. Humans in the safe zone can tag zombies who they feel may attack them as they leave the zone.
Safe zones include:
</p>
<ul>
		<li>Canteens</li>
		<li>Campus Residences</li>
		<li>The Street</li>
		<li>Nubar</li>
		<li>Spar</li>
		<li>Old Bar</li>
		</ul>
<p>Players found to be breaking any rules, intentionally or not, may be penalized in some way, or asked to leave the game entirely.  All players are required to read the rules completely, so ignorance is no excuse.</p>
<h4><img src="./images/human.png" /></h4>
<ul style="width: 800px;">
<li>You are not in play while you are off campus. Try not to leave campus for longer than 24 hours. If you need to do this contact moderators to remove you from the game.</li>
<li>You must keep an ID card with your unique identification number on you at all times.</li>
<li> Humans may stun a Zombie for 15 minutes by blasting them with a dart blaster or throwing a sock at them.</li>
<li> When tagged by a Zombie, a Human is required to distribute their ID card. One hour after being tagged, tie your bandanna around your head. You are now a member of the Zombie team! Go tag some Humans.</li>
<li>Humans must wear a headband around their upper arm to identify them as players of the game. (This headband will come in handy when you become a zombie!)</li>
</ul>
<h4><img src="./images/zombie.png" /></h4>
<ul>
<li> Zombies must feed every 24 hours. A zombie feeds by reporting their tag on the website, you MUST report the kill within 2 hours. </li>
<li> Zombies must wear a headband while outside or in safe-zones. The Original Zombies do not need to wear headbands for the first hour or until they get five kills, whichever comes first. The headband cannot be hidden under hats or hoods. The headband may be taken off in no-play zones.</li>
<li> A tag is a firm touch to any part of a Human. After tagging a Human the Zombie must collect their ID card and report the tag.</li>
<li> When hit with a dart or a sock, a Zombie is stunned for 15 minutes. A stunned zombie may not interact with the game in any way. This includes shielding other zombies from bullets or continuing to run toward a human. If shot while stunned, the zombie's stun timer is reset back to 15 minutes.</li>
</ul>
<h4><img src="./images/other.png" /></h4>
<ul>
<li>NEVER blast a non player, this is a bannable offence.</li>
<li>Athletes: Athletes are safe during official practices, but not on the way to or from practice.</li>
<li>Non players must not interfere with the game, in ways such as spying for a player.</li>
<li>A zombie must have both feet outside of a safe zone to tag a human. Humans can stun zombies from inside of a safe-zone.</li>
<li>Zombies may not attempt to damage or steal human weaponry including bullets.</li>
<li>Zombies may not use shields to deflect foam darts or socks.</li>
<li>Missions may change what is considered a safe zone.</li>
<li>Human deaths during missions don't count.</li>
<li>Zombies cannot steal guns or ammo from humans.</li>
<li>Keep your student ID on you at all times, don't argue with security if they have an issue with us running around with nerf guns.</li>
<li>Zombies can run.</li>
<li>Please, don't be a douche.</li>
</ul>

ZZZ;


HVZ_make_page( $title, $b, 0 );

?>
