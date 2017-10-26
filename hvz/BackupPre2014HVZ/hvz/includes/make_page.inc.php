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
This include provides the make_page() function which outputs the given page
to the browser. Arguments to this function are detailed in the comments
for it below. All pages in the site should go through this function. This
is essentially a templating system. This is useful because alternate styling
for, say, a printer-friendly version, could be added via a function here.
*/

require_once( "database.inc.php" );
require_once( "menu.inc.php" );

$db = new DB();
$db->connect();

// Outputs the given page to the browser.
//
// @param $title  Title of the page, both for the head-title and to be displayed
//                   at the top of the page.
//
// @param $body  Body of the page, will be placed in its own table cell.
//
// @param $access  0 - Anyone can view this page.
//                 1 - Only logged in users can view this page
//                 2 - Only admins (that are logged in) can view this page
//
// @param $root_path  Relative path to the doc root from the page
//
function hvz_make_page( $title, $body, $access, $root_path = '' )
{
    global $HVZ_stylesheets, $HVZ_menu;
    global $db;
    global $HVZ_site_title_prefix, $HVZ_site_title_suffix;

    // check access levels...
    
    if( !isset($_SESSION['uid']) )
    {
        $user_access = 0;
    }
    else
    {
        $uid = $_SESSION['uid'];
        $user_info = $db->get_user_info( $uid );
        
        if( $user_info['admin'] == TRUE )
            $user_access = 2;
        else
            $user_access = 1;
    }
    
    $display_page_ok = TRUE;
    
    if( $access > $user_access )
        $display_page_ok = FALSE;
    
    if( !$display_page_ok )
        $title = "Access Denied";

    // some intial setup of variables for use in the heredoc output later...
    
    $css_links = "";
    foreach( $HVZ_stylesheets as $cur_sheet )
    {
        $css_links .= '<link rel="stylesheet" type="text/css" href="' . $root_path . 'includes/' . $cur_sheet . '" />' . "\n";
    }
    
    $menu = "\t<ul>\n";
    foreach( $HVZ_menu as $menu_item )
    {
        if( $menu_item['access'] == $user_access || $menu_item['access'] == -1 )
        {
			
			$menu .= "\t\t";
			$menu .= '<li><a href="'.$menu_item['href'] . '">' . $menu_item['name'] . '</a><img src = "./images/divider.png" /></li>';
			$menu .= "\n";
        }
    }
    $menu .= "\t</ul>\n";
    
    echo <<<ZZZ
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>${HVZ_site_title_prefix}${title}${HVZ_site_title_suffix}</title>

$css_links


<script src="${root_path}includes/functions.js"></script>

<link rel="Shortcut Icon" href="${root_path}favicon.ico">
</head>

<body>
<div id="wrapper">
	<div id="top" class="clear">
		<h1><a href="http://games.dcu.ie/"><img src="./images/logo_normal.png" style="border: 0px;"/></a></h1>
				$menu
	</div>
<div id="maincontent">
	<div id="content">
	<div>	
		
    <h3>$title</h3>
ZZZ;

    if( $display_page_ok )
        echo $body;
    else
        echo '    <p>You do not have permission to view this page. Please ' .
                       '<a href="login.php">Login</a> first.</p>' . "\n";

    echo <<<ZZZ
</td>
</tr>
</div>
</div>
<div id="footer-links">
	<p>
		&copy; 2011 DCU Games Society in association with DCU Paranormal 
		 | 
		implemented by &copy; &reg; Paddez &trade;</a>
	</p>
</div>
</div>
</body>
</html>
ZZZ;

}

?>
