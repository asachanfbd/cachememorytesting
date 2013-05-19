<?php
/**
*   File Name: mysql.php
*   Date: 27-08-2012
*   Company: Coravity Infotech
*   Developer: Sudhanshu Mishra
*   Description: This file contains all the tables which we want to create on a particular database 
*/

$array_of_tables=array(
    'errorlog'          =>  'CREATE TABLE IF NOT EXISTS `errorlog` (
`error_id` varchar(35) NOT NULL,
`error_no` varchar(30) NOT NULL,
`error_file` varchar(500) NOT NULL,
`error_line` int(11) NOT NULL,
`error_msg` text NOT NULL,
`error_vars` longtext NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
',


    'rawstats'          =>  'CREATE TABLE IF NOT EXISTS `rawstats` (
`browser_id` varchar(32) NOT NULL,
`http_user_agent` varchar(60) NOT NULL,
`remote_address` varchar(60) NOT NULL,
`http_referer` varchar(60) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`browser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'refinedstats'      =>  'CREATE TABLE IF NOT EXISTS `refinedstats` (
`id` varchar(32) NOT NULL,
`http_user_agent` varchar(60) NOT NULL,
`agent_type` varchar(30) NOT NULL,
`agent_name` varchar(30) NOT NULL,
`agent_version` varchar(30) NOT NULL,
`os_name` varchar(30) NOT NULL,
`browser_refined` tinyint(1) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'ip2location'       =>  'CREATE TABLE IF NOT EXISTS `ip2location` (
`browser_id` varchar(32) NOT NULL,
`city_name` varchar(30) NOT NULL,
`region_name` varchar(30) NOT NULL,
`country_name` varchar(30) NOT NULL,
`country_code` varchar(30) NOT NULL,
`zip_code` varchar(30) NOT NULL,
`latitude` varchar(30) NOT NULL,
`longitude` varchar(30) NOT NULL,
`time_zone` varchar(30) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`browser_id`)
);',

    'visitordb'         =>  'CREATE TABLE IF NOT EXISTS `visitordb`(
`browser_id` varchar(32) NOT NULL,
`visitor_id` varchar(32) NOT NULL,
`visited_url` varchar(100) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',


    'users_email'       =>  'CREATE TABLE IF NOT EXISTS `users_email` (
`id` varchar(100) NOT NULL,
`email` varchar(100) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'users_info'        =>  'CREATE TABLE IF NOT EXISTS `users_info` (
`id` varchar(100) NOT NULL,
`fname` varchar(100) NOT NULL,
`lname` varchar(100) NOT NULL,
`mobile` varchar(10) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'users_logininfo'   =>  'CREATE TABLE IF NOT EXISTS `users_logininfo` (
`id` varchar(100) NOT NULL,
`password` varchar(32) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'users_notifications'=> 'CREATE TABLE IF NOT EXISTS `users_notifications` (
`id` varchar(100) NOT NULL,
`notiftype` varchar(100) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'users_privilege'    => 'CREATE TABLE IF NOT EXISTS `users_privilege` (
`id` varchar(32) NOT NULL,
`privileges` varchar(100) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'users_sessions'     => 'CREATE TABLE IF NOT EXISTS `users_sessions` (
`id` varchar(32) NOT NULL,
`sessionid` varchar(32) NOT NULL,
`ip` varchar(50) NOT NULL,
`ua` varchar(200) NOT NULL,
`expire` int(15) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`sessionid`),
KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'pages'              => '
CREATE TABLE IF NOT EXISTS `pages` (
`id` varchar(32) NOT NULL,
`type` varchar(200) NOT NULL DEFAULT \'mainnavigation\',
`name` varchar(50) NOT NULL,
`title` text NOT NULL,
`content` longtext NOT NULL,
`headerimg` varchar(200) NOT NULL DEFAULT \'images/defaultsmallslide.jpg\',
`editedby` text NOT NULL,
`seotags` mediumtext NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'page_tree'          => '
CREATE TABLE IF NOT EXISTS `page_tree` (
`name` varchar(50) NOT NULL,
`parent` varchar(50) NOT NULL,
`priority` varchar(50) NOT NULL,       
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'enquiries'          => 'CREATE TABLE IF NOT EXISTS `enquiries` ( 
    `readstatus` varchar(10) NOT NULL DEFAULT \'unread\',      
    `added` int(15) NOT NULL,
    `modified` int(15) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'dbsettings'         => 'CREATE TABLE IF NOT EXISTS `dbsettings`(
`hostname` varchar(500) NOT NULL,
`database` varchar(500) NOT NULL,
`username` varchar(500) NOT NULL,
`password` varchar(500) NOT NULL,
`added` int(15) NOT NULL,
`modified` int(15) NOT NULL,
PRIMARY KEY (`hostname`, `database`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;',

    'error_frame'        => 'CREATE TABLE IF NOT EXISTS `error_frame`(
        `id` varchar(32) NOT NULL DEFAULT \'errorstats\',
        `data` varchar(32) NOT NULL DEFAULT \'0\',
        `added` int(15) NOT NULL,
        `modified` int(15) NOT NULL
    )ENGINE=InnoDB DEFAULT CHARSET=latin1;'
);

?>