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


/*
You can make changes to the navigation menu here. Even if you don't know
any PHP, you should be able to figure it out from what is already here.

Each menu item is an associative array, which has several values:
    name - The name of the link as it appears in the menu
    href - The page that the link navigates to
    access - Access level required to see the link (for admin/user-only links) - only this level will see it
    
See make_page.inc.php for a list of access levels. -1 Indicates all users
may see the link.
    
Don't forget that you can't have a comma after the last value in an array
in PHP, or you will get weird errors.

make_page.inc.php will take care of making sure that these are prefixed by the
correct directory, so just specify all URLs relative to the doc root.

Note that because the access level is for that group only, some links may
be listed more than once (if they are available to multiple user classes).
*/

$HVZ_menu = array(
    array(
        'name'   => 'HvZ Home',
        'href'   => 'index.php',
        'access' => -1
        ),
    array(
        'name'   => 'View Game',
        'href'   => 'view_game.php?id=1',
        'access' => -1
        ),
    array(
        'name'   => 'Control Panel',
        'href'   => 'control_panel.php',
        'access' => 1
        ),
    array(
        'name'	 => 'Control Panel',
        'href'   => 'control_panel.php',
        'access' => 2
        ),
    #array(
     #   'name'   => 'Contact Admin',
      #  'href'   => 'contact_admin.php',
       # 'access' => 1
       # ),
	array(
		'name'	 => 'Info',
		'href'	 => 'missions.php',
		'access' => -1
		),
    array(
        'name'   => 'Rules',
        'href'   => 'rules.php',
        'access' => -1
        ),
    array(
        'name'   => 'Login',
        'href'   => 'login.php',
        'access' => 0
        ),
    array(
        'name'   => 'Register',
        'href'   => 'register.php',
        'access' => 0
        ),
    array(
        'name'   => 'Logout',
        'href'   => 'logout.php',
        'access' => 1
        ),
    array(
        'name'   => 'Logout',
        'href'   => 'logout.php',
        'access' => 2
        )
    );   
    
?>
