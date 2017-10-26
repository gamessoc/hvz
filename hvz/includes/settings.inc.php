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
This is the settings file for the humans vs. zombies website. Below you should
find everything you need to get software up and running.
*/

// Settings for page titles
$HVZ_site_title_prefix = 'DCU Gamessoc HvZ - '; // This goes in front of every page title in the title bar
$HVZ_site_title_suffix = '';

// MySQL Database settings
$HVZ_db_hostname = 'mysql.internal'; // host name of the MySQL server, usually 'localhost' if the MySQL server is co-located on the web server
$HVZ_db_database = 'gamessoc';
$HVZ_db_username = 'gamessoc';
$HVZ_db_password = 'BCuZeR3m';

$HVZ_db_table_prefix = 'hvz'; // If you change this you will need to fix up 

// Array of stylesheets to attach to each page (note that stylesheets should
// be placed in the includes/ directory)
$HVZ_stylesheets = array( 'hvz_styles.css' );

// Turn debugging on or off, basically sets whether or not to display
// PHP errors to the users.
$HVZ_debug = TRUE;

?>
