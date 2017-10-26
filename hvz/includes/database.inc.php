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
Here you will find the central location of all of the database-related
functions for the site. This file defines a db class, so the individual page
will need to create an instance of this in order to access database
functionality.

Note that you must call connect() on the instance before you can use
general functions. Most if not all data functions will return false if the 
database connection has not been opened yet.
*/

require_once( "settings.inc.php" );

class DB
{
    var $link; // connection resource
    var $open; // TRUE when we have an open connection
    var $table_prefix; // Prefix string for all table names
    
    // ======================== Basic Functions ============================

    // Constructor.
    function DB()
    {
        global $HVZ_db_table_prefix;
    
        $this->open = FALSE;
        
        $this->table_prefix = $HVZ_db_table_prefix;
    }
    
    // Initializes the connection based upon the settings in settings.inc.php.
    // CALL THIS FIRST, before you use data functions listed below
    //
    // @return boolean Whether the connection attempt was successful.
    function connect()
    {
        global $HVZ_db_hostname, $HVZ_db_username, $HVZ_db_password, $HVZ_db_database;
    
        $this->link = mysql_connect( $HVZ_db_hostname, $HVZ_db_username, $HVZ_db_password );
        
        if( $this->link )
            $this->open = TRUE;
        else
            return FALSE;
            
        mysql_select_db( $HVZ_db_database, $this->link );
        
        return TRUE;
    }
    
    // Closes this instance's database connection.
    function close()
    {
        if( $this->open )
        {
            mysql_close( $this->link );
            $this->open = FALSE;
        }
    }
    
    // Returns the last SQL error, or FALSE if it cannot
    function last_error()
    {
        if( !$this->open )
            return false;
    
        return mysql_error( $this->link );
    }
    
    
    // ========================== Data Functions ============================
    
    // @return Array Associative array with the given user's information, or FALSE if not found.
    function get_user_info( $uid )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'users';
            
        $sql = "SELECT * FROM $table WHERE uid='$uid'";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $info = mysql_fetch_assoc( $res );
        
        return $info;
    }
    
    // @return Array Associative array with the given user's information, or FALSE if not found.
    function get_user_info_for_email( $email )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'users';
            
        $sql = "SELECT * FROM $table WHERE email='$email'";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $info = mysql_fetch_assoc( $res );
        
        return $info;
    }

    // @return Array  Associative array with the given games's information, or FALSE if not found.
    function get_game_info( $id )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'games';
            
        $sql = "SELECT * FROM $table WHERE id='$id'";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $info = mysql_fetch_assoc( $res );
        
        return $info;
    }
    
    // @return Array  List of game ids in the database
    function get_games_list()
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'games';
            
        $sql = "SELECT id FROM $table";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $games = array();
        while( $val = mysql_fetch_row($res) )
            array_push( $games, $val[0] );
        
        return $games;
    }
    
    // @return Array  Array of associative arrays of player info for players in the given game, or FALSE if none found.
    function get_players_info( $game_id )
    {
        if( !$this->open )
            return FALSE;
            
        $prefix = $this->table_prefix;
            
        $sql = "SELECT gu.* FROM ${prefix}_games_users as gu, ${prefix}_users as u WHERE gu.game_id = '$game_id' AND gu.uid = u.uid ORDER BY u.last_name, u.first_name";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $players = array();
        while( $row = mysql_fetch_assoc($res) )
            array_push( $players, $row );
        
        return $players;
    }
    
    // @return Array  Array of associative arrays of status data points for the given game, or an empty array on failure/no data.
    function get_status_data( $gid )
    {
        if( !$this->open )
            return array();
            
        $prefix = $this->table_prefix;
            
        $sql = "SELECT * FROM ${prefix}_status WHERE gid=$gid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return array();
        
        $data = array();
        while( $row = mysql_fetch_assoc($res) )
            array_push( $data, $row );
        
        return $data;
    }
    
    
    // Inserts the given tuple with field=>value elements into the given
    // table. Returns FALSE on insertion failure.
    //
    // @param $table  Database table for the record to be inserted into (without prefix)
    // @param $tuple  Associative array of fields to be inserted
    //
    // @return  Whether insertion was accepted by SQL
    function insert_tuple( $table, $tuple )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . $table;
    
        $set_clause = $this->sqlize( $tuple );
        
        $sql = "INSERT INTO $table SET $set_clause";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        return TRUE;
    }
    
    // Updates the given tuple with field=>value elements into the given
    // table. The WHERE clause is constructed from an associative array
    // in the same manner as the SET clause. Returns FALSE on SQL failure.
    //
    // @param $table  Database table for the record to be inserted into (without prefix)
    // @param $tuple  Associative array of fields to be inserted
    // @param $where  Associative array to build the WHERE clause from
    //
    // @return  Whether update was accepted by SQL
    function update_tuple( $table, $tuple, $where )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . $table;
    
        $set_clause = $this->sqlize( $tuple );
        $where_clause = $this->sqlize( $where, ' AND ' );
        
        $sql = "UPDATE $table SET $set_clause WHERE $where_clause";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        return TRUE;
    }
    
    // Deletes the given tuple. The WHERE clause is constructed from an 
    // associative array. Returns FALSE on SQL failure.
    //
    // @param $table  Database table for the record to be inserted into (without prefix)
    // @param $where  Associative array to build the WHERE clause from
    //
    // @return  Whether update was accepted by SQL
    function delete_tuple( $table, $where )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . $table;
    
        $where_clause = $this->sqlize( $where, ' AND ' );
        
        $sql = "DELETE FROM $table WHERE $where_clause";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        return TRUE;
    }
    
    
    // ======================== Game-Related Functions ======================
    
    // Turns given player into a zombie in the given game.
    //
    // @param $gid  Game id for the game in question
    // @param $uid  Player to be infested's user id
    // @param $oz   Boolean whether or not this infestation is making an original zombie
    function infect_player( $gid, $uid, $oz = FALSE )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'games_users';
        
        $update_array = array();
        $update_array['team'] = 'Horde';
        $update_array['infected'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
        if( $oz )
            $update_array['is_oz'] = '1';
            
        $set_clause = $this->sqlize( $update_array );
        
        $sql = "UPDATE $table SET $set_clause WHERE `game_id`=$gid AND `uid`=$uid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        return TRUE;
    }
    
    // Converts given player from a zombie back to a human
    //
    // @param $gid  The id of the game in question
    // @param $uid  The id of the player in question
    function cure_player( $gid, $uid )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'games_users';
        
        $update_array = array();
        $update_array['team'] = 'Resistance';
        $update_array['infected'] = 'NULL';
        $update_array['feed_modifier'] = '0';
            
        $set_clause = $this->sqlize( $update_array );
        
        $sql = "UPDATE $table SET $set_clause WHERE `game_id`=$gid AND `uid`=$uid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        $this->mail_status_change( $gid, $uid, 'Cured' );
            
        return TRUE;
    }
    
    // Marks given player as being deceased, and sets their time of death.
    //
    // @param $gid  Game id for the game in question
    // @param $uid  Id of player in question
    function decease_player( $gid, $uid )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'games_users';
        
        $update_array = array();
        $update_array['team'] = 'Deceased';
        $update_array['starved'] = date("Y-m-d H:i:s"); // same as NOW() in MySQL
            
        $set_clause = $this->sqlize( $update_array );
        
        $sql = "UPDATE $table SET $set_clause WHERE `game_id`=$gid AND `uid`=$uid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return FALSE;
            
        $this->mail_status_change( $gid, $uid, 'Starved' );
            
        return TRUE;
    }
    
    // Returns an array of associative arrays, each with info on a kill that
    // the given player has made or shared in. The outer array is indexed
    // by the unique "kid" (kill id).
    //
    // @param $gid  The id of the game in question
    // @param $uid  User id of the player in question
    //
    // @return  An array (with possibly 0 length) of associative arrays, each of which has the following keys:
    //    'killed_id'     - uid of the player that was killed
    //    'kill_time'     - datetime of when the kill occured
    //    'divisor'       - number of players who were "in on" the kill (divide hours yield by this to find feed amount)
    //    'primary_kill'  - boolean whether or not the given player made the primary kill
    //    'kid'           - kill id for the given kill
    function get_kills( $gid, $uid )
    {
        $return_arr = array();
        
        if( !$this->open )
            return $return_arr;
            
        $table = $this->table_prefix . '_' . 'kills';
        
        $sql = "SELECT * FROM $table WHERE `gid`=$gid AND ( `killer_id`=$uid OR `share1_id`=$uid OR `share2_id`=$uid OR `share3_id`=$uid OR `share4_id`=$uid OR `share5_id`=$uid )";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
            return $return_arr;
            
        $kills = array();
        while( $row = mysql_fetch_assoc($res) )
            array_push( $kills, $row );
            
        foreach( $kills as $kill )
        {
            $entry = array();
            $entry['killed_id'] = $kill['killed_id'];
            $entry['kill_time'] = $kill['kill_time'];
            $entry['kid']       = $kill['kid'];
            
            $divisor = 1;
            for( $i = 1; $i <= 5; $i++ )
                if( $kill["share${i}_id"] )
                    $divisor++;
            $entry['divisor'] = $divisor;
            
            $entry['primary_kill'] = ( $kill['killer_id'] == $uid );
                
            $return_arr[$kill['kid']] = $entry;
        }
            
        return $return_arr;
    }
    
    // Returns an array of user ids, each of which shared in the given kill.
    //
    // @param $kid  The id of the kill in question
    // @return  An array of user ids, or an empty array if no kill shares were found
    function get_kill_shares( $kid )
    {
        if( !$this->open )
            return array();
            
        $prefix = $this->table_prefix;
            
        $sql = "SELECT * FROM ${prefix}_kills WHERE kid=$kid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return array();
          
        $data = mysql_fetch_assoc( $res );

        $shares = array();
        for( $i = 1; $i <= 5; $i++ )
            if( $data["share${i}_id"] )
                array_push( $shares, $data["share${i}_id"] );
        
        return $shares;
    }
    
    // Returns the uid of the player that killed the given player, or -1 if
    // if no killer is found.
    //
    // @param $gid  The id of the game in question
    // @param $uid  User id of the player in question
    //
    // @return  Either an integer uid, -1 for no killer found, or FALSE for error
    function get_killed_by( $gid, $uid )
    {
        if( !$this->open )
            return FALSE;
            
        $table = $this->table_prefix . '_' . 'kills';
        
        $sql = "SELECT killer_id FROM $table WHERE `gid`=$gid AND `killed_id`=$uid ORDER BY `kill_time` DESC";
        
        $res = mysql_query( $sql, $this->link );
        $killer_arr = mysql_fetch_assoc($res);
        
        if( !$killer_arr )
            return -1;
        
        $killer_id = $killer_arr['killer_id'];
        
        if( !$killer_id )
            return -1;

        return $killer_id;
    }
    
    // Calculates the number of hours the given zombie has in the given game
    // until they starve. Zero and negative numbers indicate death.
    //
    // @param $gid  The id of the game in question
    // @param $uid  The id of the zombie in question
    //
    // @return  A floating point number of hours until the given zombie starves,
    //          or FALSE if the zombie was not found in the given game
    function zombie_hours_to_starve( $gid, $uid )
    {
        if( !$this->open )
            return FALSE;

        $table = $this->table_prefix . '_' . 'games_users';
            
        $sql = "SELECT * FROM $table WHERE `uid`=$uid";
        
        $res = mysql_query( $sql, $this->link );
        
        if( !$res )
          return FALSE;
        
        $player_info = mysql_fetch_assoc( $res );
        $game_info = $this->get_game_info( $gid );
        
        $hours_to_starve = $game_info['zombie_starve_time'];
        
        // Account for basic time elapsed since infection
        $infected_datetime = $player_info['infected'];
        $infected_time = strtotime( $infected_datetime ); // MySQL to Unix timestamp
        
        if( !$infected_time )
            return $game_info['zombie_starve_time'];
        
        if( $game_info['status'] != 'Concluded' )
            $current_time = time();
        else
        {
            $end_datetime = $game_info['ended'];
            $current_time = strtotime( $end_datetime );
        }
        
        $time_since_infection = $current_time - $infected_time;
        
        $hours_to_starve -= $time_since_infection / 3600.0;
        
        // Apply feed modifier
        $hours_to_starve += $player_info['feed_modifier'];
        
        // Account for kills
        $kills = $this->get_kills( $gid, $uid );
        
        foreach( $kills as $kill_info )
        {
            $kill_datetime = $kill_info['kill_time'];
            $kill_time = strtotime( $kill_datetime );
            
            $infected_datetime = $player_info['infected'];
            $infected_time = strtotime( $infected_datetime );
            
            if( $kill_time < $infected_time ) // kill was before player became a zombie (can occur when zombie is cured and re-zombified), so don't count it
                continue;
        
            $credit = $game_info['zombie_feed_time'] / $kill_info['divisor'];
            $hours_to_starve += $credit;
        }
        
        return $hours_to_starve;
    }
    
    // Performs a check to see if any zombies have starved, and if so
    // changes them from zombie to deceased. Also checks the zombie's time
    // until starvation and sees if it has exceeded cap (over initial start time), and
    // if so adjusts the feed modifier to account for this.
    //
    // @param $gid  The id of the game to check
    function check_zombie_starve( $gid )
    {
        $players = $this->get_players_info( $gid );
        $game_info = $this->get_game_info( $gid );
        
        foreach( $players as $player )
        {
            if( $player['team'] != 'Horde' )
                continue;
        
            $uid = $player['uid'];
            
            $hours_to_starve = $this->zombie_hours_to_starve( $gid, $uid );
            
            if( $hours_to_starve <= 0 )
            {
                $this->decease_player( $gid, $uid );
            }
            elseif( $hours_to_starve > $game_info['zombie_starve_time'] )
            {
                $overrun = $hours_to_starve - $game_info['zombie_starve_time'];
                $overrun = (int)floor($overrun);
                
                $feed_modifier = $player['feed_modifier'];
                $feed_modifier -= $overrun;
                
                $update_array = array();
                $update_array['feed_modifier'] = $feed_modifier;
                
                $cond_array = array();
                $cond_array['game_id'] = $gid;
                $cond_array['uid'] = $uid;
                
                $this->update_tuple( 'games_users', $update_array, $cond_array );
            }
        }
            
    }
    
    // Calculates the number of hours since the beginning of the game, or
    // the length of time any human player has survived.
    //
    // @param $gid  The id of the game in question
    //
    // @return  A float number of hours, or FALSE on an error
    function get_survival_time( $gid )
    {
        if( !$this->open )
            return FALSE;

        $game_info = $this->get_game_info( $gid );
        
        if( $game_info['status'] == 'In Progress' )
        {
            $game_start_datetime = $game_info['started'];
            $game_start_time = strtotime( $game_start_datetime );

            $current_time = time();
            
            $time_since_start = $current_time - $game_start_time;
            
            $hours_since_start = $time_since_start / 3600.0;
        }
        elseif( $game_info['status'] == 'Concluded' )
        {
            $game_start_datetime = $game_info['started'];
            $game_start_time = strtotime( $game_start_datetime );

            $game_end_datetime = $game_info['ended'];
            $game_end_time = strtotime( $game_end_datetime );
            
            $time_since_start = $game_end_time - $game_start_time;
            
            $hours_since_start = $time_since_start / 3600.0;
        }
        else
        {
            $hours_since_start = 0;
        }
        
        return $hours_since_start;
    }
        
    // ========================== Helper Functions ===========================
    
    // Converts an associative array of key=>value pairs into `field`='value'
    // string with comma seperators suitable for use in a MySQL query.
    //
    // @param $array  The array of key=>value pairs to be sql-ized.
    // @param $seperator  Optional separator argument, useful for making
    //                    WHERE clauses with AND or OR instead of comma joins
    function sqlize( $array, $seperator = ',' )
    {
        $sql = '';
        
        foreach( $array as $k => $v )
        {
            $k = mysql_real_escape_string( $k, $this->link );
            $v = mysql_real_escape_string( $v, $this->link );
            $sql .= "`$k`='$v'$seperator";
        }
        
        $sql = substr( $sql, 0, -strlen($seperator) ); // cut off the last seperator
        
        return $sql;
    }
    
    // Mails the given player to inform them of a change in their player status
    // in the given game.
    //
    // @param $gid  Game ID
    // @param $uid  User ID
    // @param $change  One of: 'Killed', 'Infected', 'Cured', 'Starved', 'Joined'
    function mail_status_change( $gid, $uid, $change )
    {
        $user_info = $this->get_user_info( $uid );
        $game_info = $this->get_game_info( $gid );
        $players_info = $this->get_players_info( $gid );
        foreach( $players_info as $player )
            if( $player['uid'] == $uid )
                $player_info = $player;
        
        $to = $user_info['email'];
        $subject = 'DCU HvZ - Your player status has been changed';
        $message = <<<ZZZ
This is an automated message from the Humans vs. Zombies game server informing
you that your status has been changed in the game ${game_info['title']}.
ZZZ;
        switch( $change )
        {
            case 'Killed':
                $message .= <<<ZZZ


You have been reported as having been infected by a zombie player. If you 
believe that this kill was legitimate, this e-mail serves as notification that 
you may now begin playing as a zombie.
ZZZ;
                
                $killed_by = $this->get_killed_by( $gid, $uid );
                
                $kills = $this->get_kills( $gid, $killed_by );
                
                foreach( $kills as $kill )
                    if( $kill['killed_id'] == $uid )
                        $kill_info = $kill;
                $kid = $kill_info['kid'];
                $shares = $this->get_kill_shares( $kid );
                        
                $killer_info = $this->get_user_info( $killed_by );
                $killer = $killer_info['first_name'] . ' ' . $killer_info['last_name'];
                
                $kill_share_list = array();
                foreach( $shares as $share_uid )
                {
                    $user_info = $this->get_user_info( $share_uid );
                    $name = $user_info['first_name'] . ' ' . $user_info['last_name'];
                    array_push( $kill_share_list, $name );
                }
                
                //$temp = var_export( $kill_info, TRUE );
                //$message .= $temp;
                        
                $message .= <<<ZZZ


You were reported killed by:
$killer

The following zombies were reported as assisting this zombie:  

ZZZ;
                
                foreach( $kill_share_list as $player )
                    $message .= "$player\n";

                break;
                
            case 'Infected':
                $message .= <<<ZZZ


Through no fault of your own, you have been infected with the zombie virus. This
is probably the result of a mission. You can check your status by going to the website
and finding your name among the horde players.
ZZZ;
                break;
        
            case 'Cured':
                $message .= <<<ZZZ


You have been miraculously cured of your zombie infection! This is probably
as a result of a mission reward. You can check your status by going to the website
and finding your name among the human players.
ZZZ;
                break;
                
            case 'Starved':
                $message .= <<<ZZZ


You have been automatically starved out of the game, and are now deceased. You
may not report kills on the website. You may not play as a zombie, in missions
or otherwise. If there are extenuating circumstances or you have a kill that 
you have made before this message was sent but were not able to log in time, 
you can contact an admin to have this reversed.
ZZZ;
                break;
                
            case 'Joined':
                $subject = 'DCU Gamessoc HvZ - You have joined a game';
                $message .= <<<ZZZ


You have succesfully joined this game. Your Kill PIN, to be carried with you
in some fashion at all times while playing, is:
${player_info['kill_pin']}
ZZZ;
                if( $player_info['oz_pool'] )
                    $message .= <<<ZZZ

You chose to join the pool of players to be drawn from to choose the original
zombie. The zombie will be chosen before the game begins, and if you are chosen
you will be notified by e-mail, so be sure to check you e-mail before beginning
to play.             
ZZZ;

                break;
        }
        
        $message .= <<<ZZZ


DO NOT REPLY TO THIS E-MAIL. This is an automated message and the reply address
is unattended. If you wish to discuss the contents of this e-mail, believe
this message is in error, or have a question, you can log in to the website at 
http://games.dcu.ie/hvz and go to the "Contact Admin" link to 
send a message to the game admins.
ZZZ;
        
        mail( $to, $subject, $message, "From: noreply@games.redbrick.dcu.ie" );
    }
    
}

?>
