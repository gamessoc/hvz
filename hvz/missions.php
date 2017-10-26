<?php


session_start();

require_once("./includes/hvz_includes.inc.php");

$db = new DB();
$db->connect();

$title="Info";

if(isset($_SESSION['uid']))
{
	$uid = $_SESSION['uid'];
	$user_info = $db->get_user_info($uid);
}

$b = '';

$b .= <<<ZZZ
<br />
<h5>Event Overview</h5>
<p>Humans V Zombies is a massive week-long co-ordinated game of buildup tag, played anually on DCU's Glasnevin campus. There are two teams-- the Humans (who wear orange bands on their upper arms) and the Zombies (who wear orange headbands). The Zombies are slowly starving, and have to hunt Humans to stay alive! The Humans aren't defenceless, though-- armed with dart blasters and balled-up socks, they can use these projectiles to stun the Zombies, rendering them non-contagious for a short time! Throughout the week, missions take place, which can reward one of the two teams when they win-- potentially turning the tide in the apocalyptical battle! The best thing about the event is that it's totally free, and always will be.</p>
<br />
<h5>Missions</h5>
<p>Missions will take place on Monday, Tuesday and Thursday evening during the week of HvZ. These are special game scenarios set up to pit the Humans against the Zombies in different game styles, with rewards for the winning team. During missions, the rules of the game change slightly, and before and after missions, there is a 30 minute grace period-- no zombies can tag humans at these times, allowing for safe travel to and from missions. Humans cannot die during these missions, and the stun period for zombies is reduced to 60 seconds.</p>

<h4>Events</h4>
<h5>Wednesday 18th October: Zombiology (DCU Psych Soc)</h5>
<p>The first event of DCU HvZ 2017 will be hosted by Psych Soc! Want to get inside the head of the undead? Psych Soc offers a look at zombie mentality, and there's no better way to know your enemy.</p>
<p>XG01, 6pm</p>
ZZZ;

HVZ_make_page($title, $b, 0);

?>
