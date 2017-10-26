<?php

require_once( "includes/hvz_includes.inc.php" );
$message .= <<<ZZZ

Hey!
Just an update, as we are now in Day 2 of the Zombie Infection, the Original Zombies are now revealed
You are now required to wear your headbands as you would if you were a normal Zombie.

Good Luck!

ZZZ;


mail("kilian.sullivan6@mail.dcu.ie", "You are an Original Zombie!", $message, "From: noreply@games.redbrick.dcu.ie");
mail("ciara.mcdonnell53@mail.dcu.ie", "You are an Original Zombie!", $message, "From: noreply@games.redbrick.dcu.ie");
mail("eoin.coleman22@mail.dcu.ie", "You are an Original Zombie!", $message, "From: noreply@games.redbrick.dcu.ie");
mail("hiu.poon2@mail.dcu.ie", "You are an Original Zombie!", $message, "From: noreply@games.redbrick.dcu.ie");
mail("davidsmurf@gmail.com", "You are an Original Zombie!", $message, "From: noreply@games.redbrick.dcu.ie");


echo "Hey";

?>
