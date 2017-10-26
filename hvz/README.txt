
----- README for Dana's Union College Humans Vs. Zombies Code Base. -----

This code package was compiled by Dana E. Cartwright IV, alumni of Union College,
and was originally developed for playing HvZ at Union. It is being released
under the GPLv3 as a convenience to others who wish to set up an HvZ site. See
LICENESE.txt for more information on the license. Each source page has a boilerplate
copyright license notification as well.

You can contact Dana at cartwrid@gmail.com for comments and suggestions. You
can ask for help, but I will only offer up help when I have time and inclination.
Preference and faster service goes to those people who try to work out their problems
using Google and other resources before contacting me. I will not set this code
up for you unless you're willing to pay me, sorry but I just don't have time to 
do it for everyone.

Some of the code here was a little rushed, so it's a bit sloppy in places. In particular
some Union-branded stuff is hard-coded here and there. I tried to remove it all
where I could find it, and below is a list of all the files that are in need
of changes to get the site up off the ground:

mass_email.php
lost_password.php
contact_admin.php
index.php
rules.php
includes/settings.inc.php

Except for settings.inc.php, it's all straight-forward insertion of your own
text and e-mail addresses. Settings.inc.php just needs a piece of text and
your MySQL database connection settings.

The SQL DDL is located in hvz_template.sql. Run this against your newly created
database using the mysql command line tool or phpMyAdmin. In recent versions
of phpMyAdmin there should be an "import" tab, just upload the file to run
it against the database.

So, step-by-step, here is HOW TO INSTALL:

1) Get a web server account somewhere (adequate commercial hosting should be
easy to find for $10/month or less), it needs to support PHP4+ and MySQL4+. 

2) Unzip this code package and upload it to a directory on your web server.

3) For graphing, you would need to locate jpGraph in a folder called /jpGraph
in the same directory, you can get it at http://www.aditus.nu/jpgraph/. I did
not bundle it here because it is not compatible with GPLv3. Also, you would
need PHP 5.1.0 and GD 2.28, as of the version I downloaded.

4) Run the hvz_template.sql file against your MySQL database.

5) Edit the files listed above as needed.

6) Access the main page at index.php. Register your own account normally. Go
into an admin tool or run your own SQL query in order to change the "admin"
setting in (hvz)_users from '0' to '1'. This is the only way to make admins, I
didn't feel like dealing with secret codes or anything.

7) Once you login after making this change, you will have admin editing abilities.

8) Make new games manually (I use phpMyAdmin - sorry, didn't get to making an 
interface for this) by creating a new record with values:

id - leave it blank (DB will auto-assign the next numeric ID)
title - as you desire, you can change it later in the admin interface
status - "Staging"
registration - "Closed" (you can open it via the admin interface)
oz_status - "Unchosen"
created - NOW (or whatever datetime you want, in phpMyAdmin use the pull-down to select the NOW function)

All the other values are more easily edited by clicking on the "Administer This Game" link
on the game page (you must be an admin and logged in to see the link) once the game is in
database.

To make graphing work, you will have to modify the Cron script and setup the
hvz_status table yourself. Sorry, but I didn't have time to make this easy
to setup, maybe in another release.

Good luck, and enjoy,
- Dana

September 2nd, 2008
Updated: April 29th, 2010 (v1.1 - better default SQL values and README fixes)
