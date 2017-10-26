-- phpMyAdmin SQL Dump
-- version 2.11.2.2
-- http://www.phpmyadmin.net
--
-- Host: web.vu.union.edu
-- Generation Time: Apr 07, 2008 at 11:38 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `cartwrid`
--

-- --------------------------------------------------------

--
-- Table structure for table `hvz_games`
--

CREATE TABLE IF NOT EXISTS `hvz_games` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL default 'Staging',
  `registration` varchar(10) NOT NULL default 'Closed',
  `join_pass` varchar(32) default NULL,
  `oz_status` varchar(30) NOT NULL default 'Unchosen',
  `created` datetime default NULL,
  `started` datetime default NULL,
  `ended` datetime default NULL,
  `zombie_starve_time` smallint(6) NOT NULL default '24' COMMENT 'Hours',
  `zombie_stun_time` smallint(6) NOT NULL default '10' COMMENT 'Minutes',
  `zombie_feed_time` smallint(6) NOT NULL default '24' COMMENT 'Hours',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_games_users`
--

CREATE TABLE IF NOT EXISTS `hvz_games_users` (
  `game_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `oz_pool` tinyint(1) NOT NULL default '0',
  `team` varchar(30) NOT NULL default 'Resistance',
  `joined` datetime NOT NULL,
  `infected` datetime default NULL,
  `starved` datetime default NULL,
  `feed_modifier` smallint(6) NOT NULL default '0' COMMENT 'Hours',
  `is_oz` tinyint(1) default '0',
  `kill_pin` int(11) default NULL,
  PRIMARY KEY  (`game_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_kills`
--

CREATE TABLE IF NOT EXISTS `hvz_kills` (
  `kid` int(11) NOT NULL auto_increment,
  `gid` int(11) NOT NULL,
  `killer_id` int(11) NOT NULL,
  `killed_id` int(11) NOT NULL,
  `kill_time` datetime NOT NULL,
  `share1_id` int(11) default NULL,
  `share2_id` int(11) default NULL,
  `share3_id` int(11) default NULL,
  `share4_id` int(11) default NULL,
  `share5_id` int(11) default NULL,
  PRIMARY KEY  (`kid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `hvz_users`
--

CREATE TABLE IF NOT EXISTS `hvz_users` (
  `uid` int(11) NOT NULL auto_increment,
  `email` varchar(40) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `admin` tinyint(1) NOT NULL default '0',
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `class_year` varchar(50) default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;
