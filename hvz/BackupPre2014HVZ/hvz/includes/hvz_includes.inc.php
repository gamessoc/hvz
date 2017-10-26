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
This file will include all the standard includes that every page in the
site will typicall include.
*/

require_once( "settings.inc.php" );

if( $HVZ_debug )
{
    error_reporting( 6143 ); // E_ALL
    ini_set( 'display_errors', TRUE );
}
else
{
    error_reporting( 1 ); // E_ERROR
    ini_set( 'display_errors', FALSE );
}

require_once( "database.inc.php" );
require_once( "make_page.inc.php" );

?>
