<?php


session_start();

require_once("./includes/hvz_includes.inc.php");

$db = new DB();
$db->connect();

$title="Missions";

if(isset($_SESSION['uid']))
{
	$uid = $_SESSION['uid'];
	$user_info = $db->get_user_info($uid);
}

$b = '';

$b .= <<<ZZZ

<p>
Missions will appear here as they are announced during the game. <br>
- Webmaster ZedHeadEd.
<p>

ZZZ;

HVZ_make_page($title, $b, 0);

?>
